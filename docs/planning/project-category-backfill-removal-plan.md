# Project Category Backfill And Removal Plan

Date: 2026-05-02
Status: PLANNED, NOT APPROVED FOR EXECUTION

## Purpose

This plan defines the data-safe path for retiring the legacy
`projects.category` column after V1 compatibility. It is intentionally a
planning artifact, not an instruction to run a migration immediately.

## Current State

- Project category semantics are canonical in module-scoped taxonomies.
- Admin project authoring no longer accepts the legacy free-text `category`
  field.
- Public and admin project category presentation is taxonomy-first.
- `projects.category` remains a read-only fallback for existing projects that do
  not yet have project category taxonomy relations.

## Non-Goals

- Do not remove the column without a real data inventory.
- Do not infer production category mappings from local fixtures alone.
- Do not expose project taxonomy archive routes as part of this cleanup.
- Do not reintroduce free-text category authoring.

## Required Inventory

Run the inventory against the target staging or production database before any
mutation. The inventory must answer:

- how many projects have a non-empty `projects.category`
- how many of those projects have no taxonomy relation where
  `taxonomies.module = projects` and `taxonomies.type = category`
- which distinct legacy category strings exist
- which strings already match existing project category taxonomy titles or
  slugs in each active locale
- which projects have conflicting taxonomy and legacy category values
- whether any legacy values must be discarded rather than mapped

Recommended read-only query shape:

```sql
select
    p.id,
    p.title,
    p.slug,
    p.category as legacy_category,
    count(tr.id) as project_category_relation_count
from projects p
left join taxonomy_relations tr
    on tr.taxonomyable_id = p.id
    and tr.taxonomyable_type = 'App\\Models\\Project'
left join taxonomies t
    on t.id = tr.taxonomy_id
    and t.module = 'projects'
    and t.type = 'category'
where p.category is not null
  and trim(p.category) <> ''
group by p.id, p.title, p.slug, p.category
order by p.category, p.id;
```

## Mapping Rules

1. Normalize legacy strings by trimming whitespace and collapsing internal
   repeated whitespace.
2. Match an existing project category taxonomy when the normalized legacy value
   equals a taxonomy title in any active locale or a taxonomy slug.
3. If no taxonomy matches, create a project category taxonomy only when the
   operator approves the generated title and slug.
4. Generated taxonomy records must use `module = projects` and
   `type = category`.
5. If locale-specific meaning is ambiguous, keep the value in the default
   locale and copy it to other active locales only with operator approval.
6. Do not overwrite an existing taxonomy relation. Existing relations win unless
   the operator explicitly resolves a conflict.

## Dry-Run Report

Before mutation, produce a report with:

- projects that need no action
- projects that can attach an existing taxonomy
- project category taxonomies that would be created
- conflicts requiring operator approval
- values that would be intentionally discarded
- total rows to mutate
- rollback strategy and backup reference

The dry-run report is the deployment gate for the backfill migration.

## Backfill Execution

Backfill must run inside a transaction and should be restart-safe:

1. Re-run inventory and confirm the dry-run report still matches the operator
   approval.
2. Create approved missing project category taxonomies.
3. Attach missing taxonomy relations for projects without project category
   relations.
4. Leave `projects.category` unchanged for one release.
5. Re-run admin and public project category tests.
6. Smoke public project detail/archive pages in every active locale.

## Column Removal Criteria

The legacy column can be removed only after all criteria are true:

- no public or admin runtime code reads `projects.category`
- no tests depend on the fallback path
- target data inventory shows no project needs the fallback
- one release has shipped with taxonomy relations backfilled and the legacy
  column unchanged
- rollback evidence exists for the backfill release
- the migration removing the column has a tested rollback path where supported

## Rollback

Backfill rollback must detach only relations created by the backfill batch and
remove only taxonomies created by the same batch that have no other relations.
The legacy `projects.category` column remains unchanged during the backfill
release so public display can fall back if rollback is needed.

Column-removal rollback is riskier and should rely on database backup restore
for environments where re-adding the column cannot recover old values.

## Validation

Required checks for a future implementation slice:

- `php artisan test --filter=ProjectManagementTest`
- `php artisan test --filter=PublicRouteContractTest`
- targeted migration/backfill command tests
- `php artisan i18n:scan --scope=admin` if admin copy changes

## Follow-Up Implementation Slice

A later implementation task may add an Artisan command such as
`projects:category-backfill --dry-run` and `projects:category-backfill --apply`
only after the target environment inventory confirms the mappings are safe.
