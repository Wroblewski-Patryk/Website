# Task

## Header
- ID: FEA-017
- Title: Decide and harden forms/templates admin ownership contract
- Task Type: refactor
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-011
- Priority: P2
- Coverage Ledger Rows:
- Iteration:
- Operation Mode: BUILDER

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number once execution starts.
- [x] The task is aligned with repository source-of-truth documents.

## Context
FEA-011 found that forms and templates were working modules but used inline
controller validation and relied on settings-route middleware. Their ownership
needed to be made explicit before broader module cleanup.

## Goal
Confirm forms/templates as settings-owned modules and harden their create/update
validation and authorization contract.

## Scope
- Form store/update FormRequests.
- Template store/update FormRequests.
- Form and template admin controllers.
- Form/template management tests.
- Module audit, task board, project state, and MVP planning docs.

## Implementation Plan
1. Keep forms/templates under `manage-settings`.
2. Move inline store/update validation into dedicated FormRequests.
3. Authorize those requests through `manage-settings`.
4. Preserve existing controller behavior and routes.
5. Add regression coverage for validation and editor denial.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: forms/templates did not have explicit request-level contracts.
- Gaps: ownership was implicit in routes but not in validation/authorization.
- Inconsistencies: pages/posts/projects already had dedicated FormRequests.
- Architecture constraints: do not silently move modules between permission
  domains.

### 2. Select One Priority Task
- Selected task: FEA-017 forms/templates ownership hardening.
- Priority rationale: it resolves the highest-confidence FEA-011 follow-up.
- Why other candidates were deferred: `projects.category` needs a separate
  persistence/runtime audit.

### 3. Plan Implementation
- Files or surfaces to modify: FormRequests, controllers, tests, docs.
- Logic: keep settings-owned route contract and add request authorization.
- Edge cases: admin role uses Gate-before behavior, so FormRequests authorize
  through Gate rather than direct Spatie permission checks.

### 4. Execute Implementation
- Implementation notes: added four FormRequest classes and replaced inline
  validation in form/template controllers.

### 5. Verify and Test
- Validation performed: form/template management feature tests.
- Result: validation and access tests passed.

### 6. Self-Review
- Simpler option considered: document ownership only.
- Technical debt introduced: no.
- Scalability assessment: request classes can now evolve without controller
  validation sprawl.
- Refinements made: request authorization uses `Gate::allows('manage-settings')`
  to preserve admin Gate-before behavior.

### 7. Update Documentation and Knowledge
- Docs updated: module audit, implementation map, project state, MVP plan, task
  board, task evidence.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] Forms/templates ownership is explicitly confirmed as settings-owned.
- [x] Store/update validation uses dedicated FormRequests.
- [x] Request authorization uses `manage-settings`.
- [x] Regression tests cover validation and editor denial.

## Success Signal
- User or operator problem: forms/templates ownership was ambiguous.
- Expected product or reliability outcome: settings-owned modules have explicit
  validation and authorization contracts.
- How success will be observed: targeted tests pass and FEA-018 becomes the next
  local task.
- Post-launch learning needed: no.

## Deliverable For This Stage
Verified forms/templates settings-owned hardening slice.

## Constraints
- Do not change routes or UI.
- Do not move forms/templates to `manage-content`.
- Preserve existing admin/public boundaries.

## Definition of Done
- [x] Code builds without errors.
- [x] Feature works through existing admin routes.
- [x] No mock, placeholder, fake, or temporary path remains.
- [x] Full data flow works across requests, controllers, and tests.
- [x] Backend error handling exists through validation.
- [x] No existing functionality is broken.
- [x] Changes are documented in the relevant source of truth.
- [x] Behavior is reproducible from validation evidence.
- [x] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Validation Evidence
- Tests: `php artisan test --filter=FormManagementTest`;
  `php artisan test --filter=TemplateManagementTest`.
- Manual checks: controller/request diff review.
- Screenshots/logs: PHPUnit output.
- High-risk checks: no route ownership change.
- Coverage ledger updated: not applicable.
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes.
- Real API/service path used: yes.
- Endpoint and client contract match: yes.
- DB schema and migrations verified: not applicable.
- Loading state verified: not applicable.
- Error state verified: yes.
- Refresh/restart behavior verified: yes.
- Regression check performed: targeted admin feature tests.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: not applicable.
- Critical user journey: admin form/template create and update.
- SLI: validation rejects invalid payloads; editor cannot manage settings-owned
  modules.
- SLO: not applicable.
- Error budget posture: not applicable.
- Health/readiness check: not applicable.
- Logs, dashboard, or alert route: not applicable.
- Smoke command or manual smoke: targeted feature tests.
- Rollback or disable path: revert FormRequest/controller changes.

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes.
- Data classification: admin content configuration and public form definition
  data.
- Trust boundaries: admin settings-owned routes.
- Permission or ownership checks: `manage-settings` route middleware plus
  request authorization.
- Abuse cases: editor role attempts settings-owned module access.
- Secret handling: none.
- Security tests or scans: editor denial tests.
- Fail-closed behavior: unauthorized requests are forbidden.
- Residual risk: none known for this slice.

## Architecture Evidence
- Architecture source reviewed: `docs/architecture/module-contract-audit.md`.
- Fits approved architecture: yes.
- Mismatch discovered: yes, resolved for forms/templates ownership.
- Decision required from user: no.
- Approval reference if architecture changed: not applicable.
- Follow-up architecture doc updates: FEA-018.

## Deployment / Ops Evidence
- Deploy impact: low.
- Env or secret changes: none.
- Health-check impact: none.
- Smoke steps updated: no.
- Rollback note: revert request/controller changes if validation integration
  regresses.
- Observability or alerting impact: none.
- Staged rollout or feature flag: not applicable.
- `DEPLOYMENT_GATE.md` reviewed: yes.

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
- [x] Docs or context were updated.
- [x] Learning journal was updated if a recurring pitfall was confirmed.

## Result Report
- Task summary: confirmed forms/templates as settings-owned modules and added
  dedicated request validation/authorization.
- Files changed: FormRequests, form/template controllers, form/template tests,
  module audit, task board, project state, and MVP planning docs.
- How tested: targeted form/template feature tests.
- What is incomplete: project category compatibility decision remains.
- Next steps: execute FEA-018.
