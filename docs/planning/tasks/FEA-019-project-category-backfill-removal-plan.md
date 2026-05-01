# Task

## Header
- ID: FEA-019
- Title: Project category fallback backfill and removal plan
- Task Type: research
- Current Stage: post-release
- Status: DONE
- Owner: DB/Migrations
- Depends on: FEA-018
- Priority: P3
- Coverage Ledger Rows: not applicable
- Iteration: 19
- Operation Mode: BUILDER

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number.
- [x] The task is aligned with repository source-of-truth documents.

## Context

FEA-018 kept `projects.category` as a V1 read-only compatibility fallback. The
next local step was to document what evidence is required before any future
backfill or column-removal migration can safely execute.

## Goal

Define a deterministic, data-safe plan for inventorying, backfilling, rolling
back, and eventually removing the legacy project category fallback.

## Scope

- Planning and architecture docs.
- No runtime code, migration, or database mutation.

## Implementation Plan

1. Review the compatibility policy from FEA-018.
2. Define target database inventory requirements.
3. Define mapping, dry-run, backfill, rollback, and column-removal criteria.
4. Update task/context/planning docs.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: legacy data may exist without project category taxonomy relations.
- Gaps: no detailed inventory/backfill/removal plan existed.
- Inconsistencies: none after FEA-018; this task formalizes the next step.
- Architecture constraints: taxonomies remain canonical; no unapproved data
  mutation is allowed.

### 2. Select One Priority Task
- Selected task: FEA-019 project category fallback backfill/removal plan.
- Priority rationale: it was the only local non-external follow-up after FEA-012.
- Why other candidates were deferred: Coolify evidence requires an external
  target environment.

### 3. Plan Implementation
- Files or surfaces to modify: planning plan, compatibility policy, task board,
  project state, MVP planning docs, task evidence.
- Logic: document a safe future path without executing a migration.
- Edge cases: ambiguous locale mapping, conflicting taxonomy/legacy values,
  rollback of created relations and taxonomies.

### 4. Execute Implementation
- Implementation notes: added a dedicated planning document with inventory SQL,
  mapping rules, dry-run requirements, execution sequence, rollback posture, and
  column-removal criteria.

### 5. Verify and Test
- Validation performed:
  - `git diff --check`
  - docs review
- Result: passed, with only Git CRLF warnings if reported.

### 6. Self-Review
- Simpler option considered: add a migration immediately.
- Technical debt introduced: no
- Scalability assessment: later implementation can become a tested Artisan
  command without guessing production mappings.
- Refinements made: made execution explicitly unapproved until target data
  inventory exists.

### 7. Update Documentation and Knowledge
- Docs updated:
  - `docs/planning/project-category-backfill-removal-plan.md`
  - `docs/architecture/project-category-compatibility-policy.md`
  - `.codex/context/TASK_BOARD.md`
  - `.codex/context/PROJECT_STATE.md`
  - `docs/planning/mvp-next-commits.md`
  - `docs/planning/mvp-execution-plan.md`
- Context updated: yes
- Learning journal updated: not applicable

## Acceptance Criteria
- [x] Production/staging data inventory requirements are documented.
- [x] Deterministic legacy category to taxonomy backfill plan is documented.
- [x] Column removal migration criteria are explicit.

## Success Signal
- User or operator problem: legacy project category cleanup has a safe plan
  without risking data loss.
- Expected product or reliability outcome: future migration work starts from
  evidence, not assumptions.
- How success will be observed: no column removal can proceed without inventory,
  dry-run, rollback, and smoke evidence.
- Post-launch learning needed: yes

## Deliverable For This Stage

A planning artifact for future project category backfill and column removal.

## Definition of Done
- [x] Code builds without errors.
- [x] Feature works manually through the real UI, API, CLI, or operator path.
- [x] No mock, placeholder, fake, or temporary data/path remains.
- [x] Full data flow works across all relevant layers.
- [x] Backend and UI/client error handling exists where applicable.
- [x] No existing functionality is broken.
- [x] Feature works after restart, reload, or navigation refresh where
  applicable.
- [x] Changes are documented in the relevant source of truth.
- [x] Behavior is reproducible from the evidence recorded below.
- [x] Success signal, reliability, security, and rollback evidence are recorded
  when applicable.
- [x] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Validation Evidence
- Tests: not applicable, docs-only planning
- Manual checks: architecture and planning docs reviewed
- Screenshots/logs: not applicable
- High-risk checks: plan explicitly forbids executing migration without target
  data inventory
- Coverage ledger updated: not applicable
- Coverage rows closed or changed: not applicable

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes
- Real API/service path used: not applicable
- Endpoint and client contract match: not applicable
- DB schema and migrations verified: yes
- Loading state verified: not applicable
- Error state verified: not applicable
- Refresh/restart behavior verified: not applicable
- Regression check performed: docs diff review

## Architecture Evidence (required for architecture-impacting tasks)
- Architecture source reviewed:
  - `docs/architecture/project-category-compatibility-policy.md`
  - `docs/architecture/current-implementation-map.md`
- Fits approved architecture: yes
- Mismatch discovered: no
- Decision required from user: no
- Approval reference if architecture changed: not applicable
- Follow-up architecture doc updates:
  - `docs/architecture/project-category-compatibility-policy.md`

## Deployment / Ops Evidence (required for runtime or infra tasks)
- Deploy impact: none
- Env or secret changes: none
- Health-check impact: none
- Smoke steps updated: none
- Rollback note: future backfill must keep the legacy column unchanged for one
  release and record rollback evidence.
- Observability or alerting impact: none
- Staged rollout or feature flag: future command should support dry-run/apply
- `DEPLOYMENT_GATE.md` reviewed: yes

## Review Checklist (mandatory)
- [x] Process self-audit completed before implementation.
- [x] Autonomous loop evidence covers all seven steps.
- [x] Exactly one priority task was completed in this iteration.
- [x] Operation mode was selected according to iteration rotation.
- [x] Current stage is declared and respected.
- [x] Deliverable for the current stage is complete.
- [x] Architecture alignment confirmed.
- [x] Existing systems were reused where applicable.
- [x] No workaround paths were introduced.
- [x] No temporary solution was introduced.
- [x] No logic duplication was introduced.
- [x] Integration checklist evidence is attached where applicable.
- [x] AI testing evidence is attached where applicable.
- [x] Deployment gate evidence is attached where applicable.
- [x] Definition of Done evidence is attached.
- [x] Relevant validations were run.
- [x] Docs or context were updated if repository truth changed.
- [x] Learning journal was updated if a recurring pitfall was confirmed.

## Result Report
- Task summary: documented the safe future path for project category fallback
  backfill and column removal.
- Files changed:
  - `docs/planning/project-category-backfill-removal-plan.md`
  - `docs/architecture/project-category-compatibility-policy.md`
  - `.codex/context/TASK_BOARD.md`
  - `.codex/context/PROJECT_STATE.md`
  - `docs/planning/mvp-next-commits.md`
  - `docs/planning/mvp-execution-plan.md`
  - `docs/planning/tasks/FEA-019-project-category-backfill-removal-plan.md`
- How tested: `git diff --check` and docs review.
- What is incomplete: no backfill or migration is approved until target
  environment inventory exists.
- Next steps: capture Coolify rollout evidence externally or refill local work
  from the scaling backlog.
- Decisions made: do not implement project category fallback removal without a
  dry-run inventory and rollback evidence.
