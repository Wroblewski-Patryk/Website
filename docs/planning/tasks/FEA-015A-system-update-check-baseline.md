# Task

## Header
- ID: FEA-015A
- Title: Implement verified System Update Manager update-check baseline
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: architecture approval in `docs/architecture/system-update-manager-contract.md`
- Priority: P1
- Coverage Ledger Rows: not applicable
- Iteration: 2
- Operation Mode: BUILDER

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number.
- [x] The task is aligned with repository source-of-truth documents.

## Context
FEA-015 is too broad for a single safe iteration. The current repository
already contains the first update-check implementation slice, so this task
verifies and documents that baseline while keeping apply drivers out of scope.

## Goal
Ensure Featherly can safely discover update availability from a trusted manifest
through admin and scheduler paths without applying runtime code changes.

## Scope
- `app/Services/SystemUpdates/UpdateManager.php`
- `app/Console/Commands/CheckForApplicationUpdates.php`
- `app/Http/Controllers/Admin/SettingController.php`
- `config/updates.php`
- `routes/admin.php`
- `routes/console.php`
- `resources/js/Pages/Admin/Settings/Index.vue`
- `tests/Feature/SystemUpdateCheckCommandTest.php`
- `tests/Feature/Admin/SettingsManagementTest.php`
- architecture, operations, project state, task board, and execution-plan docs

## Implementation Plan
1. Inspect the approved update manager architecture and current implementation.
2. Verify the existing update-check slice covers admin status, manual check,
   scheduler check, trusted manifest parsing, and fail-closed invalid manifest
   behavior.
3. Keep automatic apply drivers out of scope.
4. Update docs and task evidence to show what is implemented and what remains.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: FEA-015 had a large scope mixing discovery, settings, scheduler,
  drivers, apply, rollback, docs, and security evidence.
- Gaps: apply drivers and preflight UI are not implemented.
- Inconsistencies: none blocking for the update-check baseline.
- Architecture constraints: update checks are app-owned; code replacement must
  be delegated to safe environment drivers; unsupported environments fail
  closed to manual/status mode.

### 2. Select One Priority Task
- Selected task: verify and close the update-check baseline slice.
- Priority rationale: it establishes a safe foundation for later driver work.
- Why other candidates were deferred: automatic apply drivers have higher blast
  radius and need separate preflight, rollback, and security evidence.

### 3. Plan Implementation
- Files or surfaces to modify: docs/context only after confirming code tests.
- Logic: preserve the current fail-closed manual/status behavior and document
  remaining driver work.
- Edge cases: missing manifest URL, invalid manifest payload, disabled checks,
  unsupported automatic apply.

### 4. Execute Implementation
- Implementation notes: current code already provides the baseline; this
  iteration verified it and synchronized architecture/operations/task docs.

### 5. Verify and Test
- Validation performed:
  - `php artisan test --filter=SystemUpdateCheckCommandTest`
  - `php artisan test --filter=SettingsManagementTest`
- Result: both targeted suites passed.

### 6. Self-Review
- Simpler option considered: mark all of FEA-015 done.
- Technical debt introduced: no.
- Scalability assessment: splitting update discovery from apply drivers keeps
  the dangerous part of the system explicit and testable.
- Refinements made: remaining FEA-015 scope now focuses on apply drivers and
  preflight UI.

### 7. Update Documentation and Knowledge
- Docs updated: architecture map, update manager contract, reliability doc,
  project state, task board, execution plan, task record.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] Admin settings can show update status/preferences.
- [x] Manual admin update check records available release status.
- [x] Scheduled command respects disabled checks and fails closed on invalid
  manifests.

## Success Signal
- User or operator problem: admins need visibility into available updates
  without unsafe self-mutation.
- Expected product or reliability outcome: update discovery is available while
  automatic application remains blocked until safe drivers exist.
- How success will be observed: targeted feature tests pass and docs identify
  update-check baseline versus remaining apply work.
- Post-launch learning needed: yes, after real manifest source is configured.

## Deliverable For This Stage
Verified update-check baseline and synchronized documentation.

## Validation Evidence
- Tests:
  - `php artisan test --filter=SystemUpdateCheckCommandTest` passed.
  - `php artisan test --filter=SettingsManagementTest` passed.
- Manual checks: inspected update manager service, admin settings route,
  scheduler registration, config, and settings UI.
- High-risk checks: confirmed automatic code application is not implemented in
  this slice.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: yes
- Critical user journey: update availability check does not mutate runtime code
- SLI: manifest check records status/failure in settings
- SLO: scheduled check should not block public serving
- Error budget posture: healthy
- Smoke command or manual smoke: `php artisan updates:check`
- Rollback or disable path: unset manifest URL or disable
  `update_checks_enabled`; automatic apply is unavailable

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes
- Trust boundaries: browser can trigger server-side check but cannot see deploy
  secrets or apply code updates
- Permission or ownership checks: admin settings routes are protected by
  `manage-settings`
- Secret handling: manifest URL remains server config; no deploy token is
  exposed
- Fail-closed behavior: invalid manifest writes failed status and no update is
  applied
- Residual risk: real apply drivers still need separate threat model and tests

## Architecture Evidence
- Architecture source reviewed:
  - `docs/architecture/system-update-manager-contract.md`
  - `docs/architecture/current-implementation-map.md`
- Fits approved architecture: yes
- Mismatch discovered: no
- Decision required from user: no
- Follow-up architecture doc updates: apply driver implementation details when
  those drivers are built

## Deployment / Ops Evidence
- Deploy impact: low
- Env or secret changes: optional `FEATHERLY_UPDATE_MANIFEST_URL`,
  `FEATHERLY_UPDATE_CHANNEL`, `FEATHERLY_AUTO_UPDATES_ENABLED`, `APP_VERSION`
- Smoke steps updated: reliability doc references `php artisan updates:check`
- Rollback note: disable checks or unset manifest URL
- Observability or alerting impact: admin status and audit events
- Staged rollout or feature flag: update checks can be disabled in settings;
  automatic apply unavailable
- `DEPLOYMENT_GATE.md` reviewed: yes

## Review Checklist
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
- Task summary: verified and documented update-check/status baseline.
- Files changed: docs/context/task-board updates only in this iteration.
- How tested: targeted update command and settings tests passed.
- What is incomplete: automatic apply drivers, preflight UI, rollback execution.
- Next steps: implement manual driver instructions and driver preflight status,
  then Coolify/archive driver fakes.
- Decisions made: keep update application out of this baseline.
