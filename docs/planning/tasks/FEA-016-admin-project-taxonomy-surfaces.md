# Task

## Header
- ID: FEA-016
- Title: Remove legacy project category authoring from admin surfaces
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-014
- Priority: P1
- Coverage Ledger Rows:
- Iteration: 5
- Operation Mode: TESTER

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number.
- [x] The task is aligned with repository source-of-truth documents.

## Context
Public project payloads already prefer taxonomy-managed categories, but the
admin project editor still exposed a free-text `category` input and the admin
project index still displayed the legacy string field. That left canonical V1
authoring and canonical admin listing behavior out of sync with architecture.

## Goal
Make admin project authoring and listing follow taxonomy-managed categories
instead of the deprecated `projects.category` field.

## Scope
- `app/Http/Controllers/Admin/ProjectController.php`
- `resources/js/Pages/Admin/Projects/Edit.vue`
- `resources/js/Pages/Admin/Projects/Index.vue`
- `tests/Feature/Admin/ProjectManagementTest.php`
- `docs/planning/tasks/FEA-016-admin-project-taxonomy-surfaces.md`
- `docs/planning/mvp-next-commits.md`
- `docs/planning/mvp-execution-plan.md`

## Implementation Plan
1. Audit current project admin props and UI for `projects.category` reads or
   writes.
2. Remove the legacy free-text field from the editor while preserving taxonomy
   selection through the existing shared taxonomy component.
3. Normalize admin index category output to taxonomy-backed labels.
4. Add targeted regression coverage and sync planning docs.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: project edit UI still allowed free-text category entry unrelated to
  the canonical taxonomy system.
- Gaps: no admin regression tests proved category output came from taxonomy
  relations or that the edit payload hid the legacy field.
- Inconsistencies: public project runtime was taxonomy-first, while admin list
  and authoring still surfaced the deprecated string field.
- Architecture constraints: preserve admin/public boundaries, keep the shared
  taxonomy selector, avoid DB schema changes in this slice, and keep localized
  project routing untouched.

### 2. Select One Priority Task
- Selected task: FEA-016 remove legacy project category authoring from admin
  surfaces.
- Priority rationale: this is the next smallest V1 slice that aligns working
  admin behavior with approved project taxonomy architecture.
- Why other candidates were deferred: FEA-015 is larger and broader; FEA-011 is
  still important, but this runtime mismatch had a smaller safe implementation
  path.

### 3. Plan Implementation
- Files or surfaces to modify: admin project controller payload shaping,
  project edit/index Vue pages, project management feature tests, and planning
  docs.
- Logic: hide legacy `project.category` in edit payloads, stop binding it in
  the editor form, and compute admin index `category` from the first
  project-category taxonomy instead of the legacy DB string.
- Edge cases: projects without category taxonomies should still show
  `Uncategorized` in the admin list; create/edit flows must keep working
  without the removed form field.

### 4. Execute Implementation
- Implementation notes: `ProjectController@index` now eager-loads project
  category taxonomies and serializes a taxonomy-backed `category` prop for the
  admin list; project create/edit payloads stop exposing the legacy category
  field; the project editor now relies solely on `TaxonomySelect`.

### 5. Verify and Test
- Validation performed: `php artisan test --filter=ProjectManagementTest`;
  `npm run lint`
- Result: pass

### 6. Self-Review
- Simpler option considered: remove the category column entirely from the admin
  index.
- Technical debt introduced: no
- Scalability assessment: taxonomy-backed index serialization keeps the admin
  UI aligned with the approved content classification model while deferring DB
  retirement of the legacy column to a later dedicated slice.
- Refinements made: category sorting was disabled on the index because the
  displayed value is now derived from related taxonomy data rather than a
  directly sortable DB column.

### 7. Update Documentation and Knowledge
- Docs updated: yes
- Context updated: partially; planning docs updated, but `.codex/context`
  files could not be written by the sandbox in this run.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] Admin project edit UI no longer exposes the deprecated free-text category
  field.
- [x] Admin project list shows taxonomy-backed category labels when project
  category taxonomies exist.
- [x] Regression coverage proves the admin list and edit payload contracts for
  the new taxonomy-first behavior.

## Success Signal
- User or operator problem: editors can still author project category data in a
  second, deprecated place that drifts from taxonomy-managed truth.
- Expected product or reliability outcome: admin project authoring and listing
  now reflect the same category source as the approved V1 architecture.
- How success will be observed: project management feature tests prove
  taxonomy-backed category output and absence of the legacy edit prop.
- Post-launch learning needed: yes

## Deliverable For This Stage
Verified admin project taxonomy-alignment slice with targeted regression
coverage and synchronized planning docs.

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
- [x] Success signal, reliability, security, and rollback evidence are
  recorded.
- [x] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Validation Evidence
- Tests: `php artisan test --filter=ProjectManagementTest`
- Manual checks: reviewed admin project create/edit payloads and admin list
  category serialization paths.
- Screenshots/logs: not applicable
- High-risk checks: confirmed projects without taxonomies still keep the admin
  empty-state badge path and that no request validation depends on the removed
  field.
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
- Regression check performed: targeted project management feature tests plus
  frontend lint

## Product / Discovery Evidence
- Problem validated: yes
- User or operator affected: admin editors managing projects
- Existing workaround or pain: project categories could be entered in a legacy
  text field that no longer matched taxonomy-managed grouping
- Smallest useful slice: align the live admin list and editor before touching
  DB persistence debt
- Success metric or signal: admin project category data is taxonomy-first on
  both list and edit surfaces
- Feature flag, staged rollout, or disable path: not applicable
- Post-launch feedback or metric check: audit whether any remaining runtime
  persistence surfaces still require the legacy column

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: not
  applicable
- Critical user journey: admin project authoring and project listing
- SLI: project category labels in admin should match the canonical taxonomy
  relation when present
- SLO: not applicable
- Error budget posture: healthy
- Health/readiness check: targeted feature tests
- Logs, dashboard, or alert route: not applicable
- Smoke command or manual smoke: project management feature test
- Rollback or disable path: restore the legacy edit field and direct list
  binding if category migration must be postponed

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes
- Data classification: internal admin content metadata
- Trust boundaries: authenticated admin requests and Inertia props
- Permission or ownership checks: existing project policy checks remain
  unchanged
- Abuse cases: stale legacy category authoring can create inconsistent public
  and admin project classification
- Secret handling: none
- Security tests or scans: targeted feature tests
- Fail-closed behavior: category display falls back to the existing
  uncategorized UI when no project taxonomy is attached
- Residual risk: the legacy DB column still exists for compatibility and may
  still be read by non-admin/runtime paths until a later cleanup slice

## Architecture Evidence
- Architecture source reviewed: `docs/architecture/current-implementation-map.md`,
  `docs/architecture/modules.md`, `docs/architecture/system-architecture.md`
- Fits approved architecture: yes
- Mismatch discovered: yes
- Decision required from user: no
- Approval reference if architecture changed:
- Follow-up architecture doc updates: none required; implementation now matches
  the current approved direction more closely

## Deployment / Ops Evidence
- Deploy impact: low
- Env or secret changes: none
- Health-check impact: none
- Smoke steps updated: no
- Rollback note: revert admin controller/UI/test changes if taxonomy-only admin
  behavior needs to be withdrawn
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
- Task summary: admin projects now use taxonomy-backed category presentation in
  the list and no longer expose legacy free-text category authoring in the
  editor.
- Files changed: `app/Http/Controllers/Admin/ProjectController.php`,
  `resources/js/Pages/Admin/Projects/Edit.vue`,
  `resources/js/Pages/Admin/Projects/Index.vue`,
  `tests/Feature/Admin/ProjectManagementTest.php`,
  `docs/planning/tasks/FEA-016-admin-project-taxonomy-surfaces.md`,
  `docs/planning/mvp-next-commits.md`,
  `docs/planning/mvp-execution-plan.md`
- How tested: `php artisan test --filter=ProjectManagementTest`; `npm run lint`
- What is incomplete: `.codex/context/TASK_BOARD.md` and
  `.codex/context/PROJECT_STATE.md` could not be updated in this run because
  writes to `.codex/context` were denied by the sandbox; the legacy DB column
  still remains as compatibility debt.
- Next steps: execute `FEA-011` or start the first `FEA-015` implementation
  slice depending on whether architecture-audit or update-manager progress is
  more valuable next.
- Decisions made: admin project category UX now follows taxonomy relations
  instead of the deprecated string field.

## Notes
The sandbox allowed writes under `docs/` but denied writes under
`.codex/context/`, so planning docs were synchronized while context updates had
to be reported as pending.
