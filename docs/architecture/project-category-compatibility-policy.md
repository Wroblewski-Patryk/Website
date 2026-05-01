# Project Category Compatibility Policy

Date: 2026-05-02
Status: APPROVED FOR V1 COMPATIBILITY

## Purpose

Project category semantics are owned by module-scoped taxonomies. The legacy
`projects.category` column remains only as read-only compatibility data for
existing installations and historical fixtures until a dedicated backfill and
column-removal migration is planned.

## Current Contract

- Editors create and update project category assignments through project
  taxonomies.
- Admin project create/update requests do not validate or persist a free-text
  `category` payload.
- Admin project edit payloads hide the legacy `category` field.
- Admin project index category labels are serialized from project category
  taxonomies.
- Public project detail, project archive, and shared `all_projects` payloads use
  `ProjectPublicPresenter`, which prefers project category taxonomies and only
  falls back to `projects.category` when no category taxonomy is attached.
- Public taxonomy archive URLs remain posts-only in V1; project category
  taxonomies are presentation metadata, not public archive routes.

## Compatibility Decision

Keep `projects.category` through V1 as a read-only fallback.

The column must not be reintroduced into admin authoring surfaces or treated as
canonical project metadata. Removing the column now would risk dropping existing
category labels from installations that have not been backfilled into taxonomy
relations. A later migration can retire the column only after it can prove one
of these outcomes for every project:

- a project category taxonomy relation already exists
- a legacy category string was mapped to a project category taxonomy
- the legacy value was intentionally discarded through an operator-approved data
  cleanup plan

## Retirement Path

1. Inventory production/staging data for non-empty `projects.category` values
   without project category taxonomy relations.
2. Define a deterministic mapping from legacy strings to module-scoped project
   category taxonomies, including locale behavior for generated taxonomy
   titles/slugs.
3. Run a dry-run backfill report before mutating data.
4. Backfill taxonomy relations inside a transaction and keep the legacy column
   unchanged for one release.
5. Remove the `ProjectPublicPresenter` fallback and the database column in a
   later migration only after rollback and smoke evidence exists.

## Guardrails

- Do not add new writes to `projects.category`.
- Do not sort, filter, or search active project category UX by the legacy field.
- Do not expose project taxonomy archive URLs until a separate product decision
  changes the V1 posts-only taxonomy archive contract.
- Keep regression tests proving taxonomy-first admin and public project category
  presentation.

## Verified Surfaces

- `app/Http/Requests/Admin/Project/StoreProjectRequest.php`
- `app/Http/Requests/Admin/Project/UpdateProjectRequest.php`
- `app/Http/Controllers/Admin/ProjectController.php`
- `app/Support/ProjectPublicPresenter.php`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `database/migrations/2026_03_01_145808_create_projects_table.php`
- `tests/Feature/Admin/ProjectManagementTest.php`
- `tests/Feature/PublicRouteContractTest.php`
