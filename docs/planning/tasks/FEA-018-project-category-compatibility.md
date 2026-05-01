# Task

## Header
- ID: FEA-018
- Title: Decide project category compatibility retirement path
- Task Type: research
- Current Stage: post-release
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-011
- Priority: P2
- Coverage Ledger Rows: not applicable
- Iteration: 18
- Operation Mode: BUILDER

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number.
- [x] The task is aligned with repository source-of-truth documents.

## Context

FEA-014 and FEA-016 moved public and admin project category presentation to
module-scoped project taxonomies. The legacy `projects.category` column still
exists in the database and public presenter fallback path, so the architecture
needed an explicit compatibility decision before later cleanup work.

## Goal

Audit remaining legacy project category surfaces and record whether
`projects.category` should be removed now, retained as compatibility debt, or
retired through a later migration plan.

## Scope

- Architecture and planning docs for project category compatibility.
- Code reads across project admin requests/controllers, public presenter,
  shared Inertia project payloads, migration schema, and regression tests.
- No runtime code or DB schema changes in this slice.

## Implementation Plan

1. Inspect the existing project category read/write surfaces.
2. Identify active authoring paths versus compatibility-only reads.
3. Choose the smallest safe policy that preserves data.
4. Update architecture, task board, project state, and planning evidence.
5. Re-run targeted project category contract tests.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: `projects.category` still exists in the schema and public fallback.
- Gaps: no explicit retirement/backfill policy existed.
- Inconsistencies: admin/public presentation is taxonomy-first, but the legacy
  column remains available for older records.
- Architecture constraints: module-scoped taxonomies are canonical for project
  grouping; V1 public taxonomy archives remain posts-only.

### 2. Select One Priority Task
- Selected task: FEA-018 project category compatibility decision.
- Priority rationale: it was the first READY task after forms/templates
  ownership hardening.
- Why other candidates were deferred: FEA-012 depends on the module/category
  decisions being settled; FEA-015 remains blocked on external Coolify evidence.

### 3. Plan Implementation
- Files or surfaces to modify: architecture policy, module audit, current map,
  task board, project state, MVP planning docs, task evidence.
- Logic: keep the legacy field as read-only fallback, not an authoring source.
- Edge cases: existing projects without taxonomy relations must not lose their
  displayed category label before a backfill exists.

### 4. Execute Implementation
- Implementation notes: documented the V1 compatibility policy and queued a
  later FEA-019 data inventory/backfill/removal plan instead of removing the
  column in this slice.

### 5. Verify and Test
- Validation performed:
  - `php artisan test --filter=ProjectManagementTest`
  - `php artisan test --filter=PublicRouteContractTest`
- Result: passed.

### 6. Self-Review
- Simpler option considered: remove the database column immediately.
- Technical debt introduced: no
- Scalability assessment: taxonomy remains the scalable model; the fallback is
  bounded by an explicit retirement policy.
- Refinements made: separated V1 compatibility from later DB migration work.

### 7. Update Documentation and Knowledge
- Docs updated:
  - `docs/architecture/project-category-compatibility-policy.md`
  - `docs/architecture/module-contract-audit.md`
  - `docs/architecture/current-implementation-map.md`
  - `docs/planning/mvp-next-commits.md`
  - `docs/planning/mvp-execution-plan.md`
  - `.codex/context/PROJECT_STATE.md`
  - `.codex/context/TASK_BOARD.md`
- Context updated: yes
- Learning journal updated: not applicable

## Acceptance Criteria
- [x] Remaining `projects.category` read/write surfaces are audited.
- [x] Compatibility versus removal decision is documented.
- [x] Follow-up migration/backfill criteria are explicit.

## Success Signal
- User or operator problem: future cleanup can happen without risking silent
  data loss for existing project categories.
- Expected product or reliability outcome: project categories remain
  taxonomy-first while legacy data still renders.
- How success will be observed: admin/public tests continue proving
  taxonomy-first project category presentation.
- Post-launch learning needed: yes

## Deliverable For This Stage

Repository source-of-truth documents now record the compatibility decision and
the safe retirement path.

## Constraints
- use existing systems and approved mechanisms
- preserve locale-aware route boundaries and the admin/public split
- preserve block-builder content contracts and shared admin form primitives
- keep translation and i18n scanning implications explicit
- do not introduce new structures without approval
- do not implement workarounds
- do not duplicate logic
- stay within the declared current stage unless explicit approval changes it
- no placeholders, mock-only paths, or temporary solutions in delivered behavior
- implement features as a vertical slice across UI, logic, API, DB, validation,
  error handling, and tests when the task affects runtime behavior

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

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Validation Evidence
- Tests:
  - `php artisan test --filter=ProjectManagementTest`
  - `php artisan test --filter=PublicRouteContractTest`
- Manual checks: code reads of project category request/controller/presenter
  surfaces.
- Screenshots/logs: not applicable
- High-risk checks: verified no runtime write path was added for
  `projects.category`.
- Coverage ledger updated: not applicable
- Coverage rows closed or changed: not applicable

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes
- Real API/service path used: yes
- Endpoint and client contract match: yes
- DB schema and migrations verified: yes
- Loading state verified: not applicable
- Error state verified: not applicable
- Refresh/restart behavior verified: not applicable
- Regression check performed: project admin and public route feature tests

## Product / Discovery Evidence
- Problem validated: yes
- User or operator affected: site operators with existing project category data
- Existing workaround or pain: legacy free-text category could drift from
  taxonomy relations if reintroduced.
- Smallest useful slice: document the compatibility policy and queue later
  migration work.
- Success metric or signal: project category output remains taxonomy-first in
  tests.
- Feature flag, staged rollout, or disable path: not applicable
- Post-launch feedback or metric check: data inventory before FEA-019.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: not applicable
- Critical user journey: public project category display
- SLI: taxonomy-backed project category payload correctness
- SLO: not applicable
- Error budget posture: not applicable
- Health/readiness check: not applicable
- Logs, dashboard, or alert route: not applicable
- Smoke command or manual smoke: targeted feature tests
- Rollback or disable path: docs-only change can be reverted; runtime behavior
  unchanged.

## AI Testing Evidence (required for AI features)
- `AI_TESTING_PROTOCOL.md` reviewed: not applicable
- Memory consistency scenarios: not applicable
- Multi-step context scenarios: not applicable
- Adversarial or role-break scenarios: not applicable
- Prompt injection checks: not applicable
- Data leakage and unauthorized access checks: not applicable
- Result: not applicable

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: not applicable
- Data classification: public project metadata
- Trust boundaries: admin project authoring remains authenticated and
  permission-gated.
- Permission or ownership checks: project authoring still uses existing
  `ContentPolicy` and admin route guards.
- Abuse cases: reintroducing free-text category writes could create drift.
- Secret handling: not applicable
- Security tests or scans: targeted feature tests
- Fail-closed behavior: project taxonomy archives remain posts-only in V1.
- Residual risk: later data inventory is required before removing the column.

## Architecture Evidence (required for architecture-impacting tasks)
- Architecture source reviewed:
  - `docs/architecture/current-implementation-map.md`
  - `docs/architecture/module-contract-audit.md`
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
- Rollback note: revert docs if policy changes.
- Observability or alerting impact: none
- Staged rollout or feature flag: not applicable
- `DEPLOYMENT_GATE.md` reviewed: not applicable

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
- Task summary: settled project category compatibility for V1 and documented
  the later safe retirement path.
- Files changed:
  - `docs/architecture/project-category-compatibility-policy.md`
  - `docs/architecture/module-contract-audit.md`
  - `docs/architecture/current-implementation-map.md`
  - `.codex/context/TASK_BOARD.md`
  - `.codex/context/PROJECT_STATE.md`
  - `docs/planning/mvp-next-commits.md`
  - `docs/planning/mvp-execution-plan.md`
  - `docs/planning/tasks/FEA-018-project-category-compatibility.md`
- How tested:
  - `php artisan test --filter=ProjectManagementTest`
  - `php artisan test --filter=PublicRouteContractTest`
- What is incomplete: actual legacy data inventory/backfill/removal is deferred
  to FEA-019.
- Next steps: FEA-012 residual legacy docs normalization.
- Decisions made: keep `projects.category` as read-only compatibility fallback
  through V1.

## Notes

The fallback is not a new source of truth. It exists only to keep old records
rendering until a data-safe migration can retire it.
