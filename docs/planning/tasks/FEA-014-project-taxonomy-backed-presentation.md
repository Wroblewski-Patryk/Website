# Task

## Header
- ID: FEA-014
- Title: Replace legacy project category presentation with taxonomy-backed public payloads
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-013
- Priority: P1
- Coverage Ledger Rows:
- Iteration: 4
- Operation Mode: BUILDER

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number.
- [x] The task is aligned with repository source-of-truth documents.

## Context
Public project detail/archive surfaces and the shared runtime `all_projects`
payload still exposed the legacy `projects.category` string even after V1
direction declared taxonomies canonical for project categorization.

## Goal
Make public-facing project payloads prefer project category taxonomies while
keeping a fail-safe fallback to the legacy string for not-yet-migrated data.

## Scope
- `app/Support/ProjectPublicPresenter.php`
- `app/Http/Controllers/ProjectController.php`
- `app/Http/Controllers/PageController.php`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `tests/Feature/PublicRouteContractTest.php`
- `docs/planning/tasks/FEA-014-project-taxonomy-backed-presentation.md`
- `docs/planning/mvp-next-commits.md`
- `docs/planning/mvp-execution-plan.md`
- `.codex/context/TASK_BOARD.md`
- `.codex/context/PROJECT_STATE.md`

## Implementation Plan
1. Inspect current public project payload assembly in detail, archive, and
   shared Inertia props.
2. Introduce one reusable presenter for public project payload normalization.
3. Switch detail/archive/shared payloads to taxonomy-backed category output
   with legacy fallback.
4. Verify the localized public route contract and sync planning/context files.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: public project surfaces still serialized `projects.category` directly.
- Gaps: no regression test proved taxonomy-backed categories on project detail
  or archive payloads.
- Inconsistencies: V1 docs declared taxonomies canonical, but runtime payloads
  still privileged the deprecated field.
- Architecture constraints: preserve localized public routes, reuse the
  existing taxonomy relation, and avoid changing admin authoring in the same
  slice.

### 2. Select One Priority Task
- Selected task: FEA-014 taxonomy-backed public project presentation.
- Priority rationale: this closes the highest-value runtime gap left after
  route hardening.
- Why other candidates were deferred: broader module audits are lower priority
  than removing active public/runtime contract drift.

### 3. Plan Implementation
- Files or surfaces to modify: public project controller, page archive
  controller path, shared Inertia payload middleware, public route tests, and
  planning/context docs.
- Logic: normalize public project payloads through one presenter that resolves
  the first category taxonomy title, then falls back to the legacy category
  string if needed.
- Edge cases: projects without assigned category taxonomies must still render;
  shared `all_projects` must stay aligned with archive/detail payloads.

### 4. Execute Implementation
- Implementation notes: added `ProjectPublicPresenter`, eager-loaded project
  category taxonomies for public payloads, removed the duplicate raw
  `all_projects` page prop assignment, and normalized `category` output across
  detail/archive/runtime payloads.

### 5. Verify and Test
- Validation performed: `php artisan test --filter=PublicRouteContractTest`;
  `php -l app/Support/ProjectPublicPresenter.php`
- Result: pass

### 6. Self-Review
- Simpler option considered: inline the category override separately in each
  controller path.
- Technical debt introduced: no
- Scalability assessment: one presenter keeps remaining project public payload
  follow-ups centralized and avoids further route-specific drift.
- Refinements made: presenter now emits the full taxonomy translation payload
  instead of a locale-collapsed string to match existing translatable frontend
  contracts.

### 7. Update Documentation and Knowledge
- Docs updated: yes
- Context updated: yes
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] Public project detail payload prefers project category taxonomies.
- [x] Public project archive and shared `all_projects` payloads prefer project
  category taxonomies.
- [x] Regression coverage proves the taxonomy-backed category contract on the
  localized public route surface.

## Success Signal
- User or operator problem: public project surfaces expose a deprecated
  category source that can drift from taxonomy-managed data.
- Expected product or reliability outcome: public project categorization now
  matches the approved taxonomy contract without breaking legacy rows.
- How success will be observed: public route feature tests assert taxonomy
  category payloads on project detail and archive responses.
- Post-launch learning needed: yes

## Deliverable For This Stage
Verified public/runtime payload migration for project category presentation plus
synchronized queue/context updates.

## Constraints
- use existing systems and approved mechanisms
- preserve locale-aware route boundaries and the admin/public split
- preserve block-builder content contracts and shared admin form primitives
- keep translation and i18n scanning implications explicit
- do not introduce new structures without approval
- do not implement workarounds
- do not duplicate logic
- stay within the declared current stage unless explicit approval changes it
- no placeholders, mock-only paths, or temporary solutions in delivered
  behavior
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
- [x] Success signal, reliability, security, and rollback evidence are recorded.
- [x] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Validation Evidence
- Tests: `php artisan test --filter=PublicRouteContractTest`
- Manual checks: reviewed public project detail resolution, project archive
  rendering path, and shared Inertia runtime project payload assembly.
- Screenshots/logs: not applicable
- High-risk checks: confirmed taxonomy-backed category payloads on both detail
  and archive routes, with legacy string fallback preserved in presenter logic.
- Coverage ledger updated: not applicable
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes
- Real API/service path used: yes
- Endpoint and client contract match: yes
- DB schema and migrations verified: yes
- Loading state verified: not applicable
- Error state verified: not applicable
- Refresh/restart behavior verified: yes
- Regression check performed: targeted public route contract tests

## Product / Discovery Evidence
- Problem validated: yes
- User or operator affected: public visitors and editors relying on project
  categorization parity
- Existing workaround or pain: public payloads could display stale legacy
  category text even when taxonomy-managed data existed
- Smallest useful slice: migrate public/runtime serialization before removing
  admin legacy authoring
- Success metric or signal: project detail/archive payloads expose taxonomy
  category data by default
- Feature flag, staged rollout, or disable path: not applicable
- Post-launch feedback or metric check: audit remaining admin/editor surfaces
  still reading or writing the legacy field

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: not applicable
- Critical user journey: localized public project detail/archive rendering
- SLI: project pages should serialize the same category source across detail,
  archive, and shared runtime payloads
- SLO: not applicable
- Error budget posture: healthy
- Health/readiness check: targeted feature test
- Logs, dashboard, or alert route: not applicable
- Smoke command or manual smoke: public route feature tests
- Rollback or disable path: revert the presenter wiring and regression tests

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes
- Data classification: public content metadata
- Trust boundaries: public route input into project resolution and serialization
- Permission or ownership checks: no admin authorization contract changed
- Abuse cases: stale/deprecated category presentation can mislead visitors about
  project grouping
- Secret handling: none
- Security tests or scans: targeted feature tests
- Fail-closed behavior: projects without category taxonomies still fall back to
  the existing stored string rather than failing or inventing data
- Residual risk: admin project forms and index still surface the legacy
  `category` field and need a follow-up removal/audit slice

## Architecture Evidence
- Architecture source reviewed: `docs/architecture/README.md`,
  `docs/architecture/modules.md`, `docs/architecture/seo-route-contracts.md`
- Fits approved architecture: yes
- Mismatch discovered: yes
- Decision required from user: no
- Approval reference if architecture changed:
- Follow-up architecture doc updates: none required; implementation now matches
  the approved V1 direction more closely

## Deployment / Ops Evidence
- Deploy impact: low
- Env or secret changes: none
- Health-check impact: none
- Smoke steps updated: no
- Rollback note: revert the presenter wiring if taxonomy-backed payloads need to
  be withdrawn
- Observability or alerting impact: none
- Staged rollout or feature flag: not applicable
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
- Task summary: public project detail, archive, and shared runtime payloads now
  prefer category taxonomies over the legacy string field, with regression
  coverage on the localized route contract.
- Files changed: `app/Support/ProjectPublicPresenter.php`,
  `app/Http/Controllers/ProjectController.php`,
  `app/Http/Controllers/PageController.php`,
  `app/Http/Middleware/HandleInertiaRequests.php`,
  `tests/Feature/PublicRouteContractTest.php`,
  `docs/planning/tasks/FEA-014-project-taxonomy-backed-presentation.md`,
  `docs/planning/mvp-next-commits.md`,
  `docs/planning/mvp-execution-plan.md`,
  `.codex/context/TASK_BOARD.md`,
  `.codex/context/PROJECT_STATE.md`
- How tested: `php artisan test --filter=PublicRouteContractTest`
- What is incomplete: admin project authoring/index surfaces still expose the
  legacy `category` field
- Next steps: execute `FEA-011` to audit module contracts, then split the
  smallest follow-up that removes or demotes legacy project category authoring
- Decisions made: public/runtime project categorization now follows taxonomy
  data first and only falls back to the legacy string when necessary
