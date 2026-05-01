# Task

## Header
- ID: FEA-015B
- Title: Implement manual/fake System Update Manager apply contract
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-015A
- Priority: P1
- Coverage Ledger Rows: not applicable
- Iteration: 3
- Operation Mode: ARCHITECT

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number.
- [x] The task is aligned with repository source-of-truth documents.

## Context
The update-check baseline exists. The next safe slice is to define the driver
apply contract and prove that apply attempts are guarded before any production
driver can mutate runtime files.

## Goal
Add a safe apply-driver contract with manual instructions and config-gated fake
driver coverage while keeping production code replacement unavailable.

## Scope
- `app/Services/SystemUpdates/UpdateDriver.php`
- `app/Services/SystemUpdates/Drivers/ManualUpdateDriver.php`
- `app/Services/SystemUpdates/Drivers/FakeUpdateDriver.php`
- `app/Services/SystemUpdates/UpdateManager.php`
- `app/Console/Commands/ApplyApplicationUpdate.php`
- `app/Http/Controllers/Admin/SettingController.php`
- `config/updates.php`
- `routes/admin.php`
- `resources/js/Pages/Admin/Settings/Index.vue`
- `database/seeders/data/translations/settings.php`
- `tests/Feature/SystemUpdateCheckCommandTest.php`
- `tests/Feature/Admin/SettingsManagementTest.php`
- architecture, reliability, planning, and context docs

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: update checks existed but apply behavior had no explicit driver
  contract.
- Gaps: no fake successful apply coverage; no manual operator instruction path.
- Inconsistencies: none blocking.
- Architecture constraints: no deploy secrets in browser; production mutation
  only through safe environment drivers; unsupported environments fail closed.

### 2. Select One Priority Task
- Selected task: manual/fake apply contract.
- Priority rationale: production driver work needs a tested contract before
  platform-specific behavior is introduced.
- Why other candidates were deferred: Coolify/archive drivers have higher
  deployment and rollback risk.

### 3. Plan Implementation
- Files or surfaces to modify: update service, drivers, command, admin route,
  settings UI, tests, docs.
- Logic: manual driver records instructions without applying; fake driver only
  applies in tests when enabled by config; block unavailable/high-risk releases.
- Edge cases: no update available, manual review release, PHP requirement
  mismatch, env kill switch, fake driver disabled.

### 4. Execute Implementation
- Implementation notes: added `UpdateDriver`, manual and fake drivers,
  `applyUpdate`, `updates:apply`, admin apply route/action, UI instructions,
  and translation keys.

### 5. Verify and Test
- Validation performed:
  - `php artisan test --filter=SystemUpdateCheckCommandTest`
  - `php artisan test --filter=SettingsManagementTest`
  - `npm run lint`
- Result: all passed.

### 6. Self-Review
- Simpler option considered: fake driver only.
- Technical debt introduced: no.
- Scalability assessment: the contract isolates driver-specific behavior and
  keeps real deployment drivers behind preflight and config.
- Refinements made: manual driver records instructions and leaves
  `update_available` true.

### 7. Update Documentation and Knowledge
- Docs updated: architecture map, update manager contract, reliability doc,
  project state, task board, execution plan, next commits, task record.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] Manual apply records operator instructions and does not mutate files.
- [x] Fake apply succeeds only when explicitly config-enabled.
- [x] Manual-review releases block apply attempts.
- [x] Admin apply action is permission-protected by the existing settings
  boundary.

## Validation Evidence
- Tests:
  - `php artisan test --filter=SystemUpdateCheckCommandTest` passed.
  - `php artisan test --filter=SettingsManagementTest` passed.
  - `npm run lint` passed.
- Manual checks: inspected admin settings UI, route, command, driver contract,
  and status fields.
- High-risk checks: real production code replacement remains unavailable.

## Reliability / Observability Evidence
- Critical user journey: apply attempt records instructions/status without
  corrupting installation files.
- SLI: `system_update_status.apply_status` and operator message are persisted.
- Smoke command: `php artisan updates:apply --force`.
- Rollback or disable path: manual driver changes no files; fake driver is
  disabled unless config-enabled.

## Security / Privacy Evidence
- Permission or ownership checks: admin route remains under `manage-settings`.
- Secret handling: no deployment secrets are added or exposed.
- Fail-closed behavior: manual-review releases and unsupported fake driver
  configuration block application.
- Residual risk: real drivers need separate threat model, preflight, checksum,
  and rollback evidence.

## Architecture Evidence
- Fits approved architecture: yes.
- Mismatch discovered: no.
- Decision required from user: no.

## Result Report
- Task summary: added safe driver apply contract, manual instructions, fake
  driver tests, command, admin action, and docs.
- Files changed: see scope.
- How tested: targeted PHPUnit tests and frontend lint.
- What is incomplete: production Coolify/archive drivers and preflight UI.
- Next steps: implement production driver preflight status before any real
  driver apply behavior.
