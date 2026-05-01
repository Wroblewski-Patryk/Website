# Task

## Header
- ID: FEA-015C
- Title: Implement production driver preflight status
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-015B
- Priority: P1
- Coverage Ledger Rows: not applicable
- Iteration: 4
- Operation Mode: BUILDER

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number.
- [x] The task is aligned with repository source-of-truth documents.

## Context
The update manager has a safe apply contract. The next step is production
driver preflight visibility before any real environment mutation is enabled.

## Goal
Expose Coolify and archive driver readiness through backend status and admin UI
without enabling production apply execution.

## Scope
- `app/Services/SystemUpdates/Drivers/CoolifyUpdateDriver.php`
- `app/Services/SystemUpdates/Drivers/ArchiveUpdateDriver.php`
- `app/Services/SystemUpdates/UpdateManager.php`
- `config/updates.php`
- `resources/js/Pages/Admin/Settings/Index.vue`
- `database/seeders/data/translations/settings.php`
- `tests/Feature/SystemUpdateCheckCommandTest.php`
- `tests/Feature/Admin/SettingsManagementTest.php`
- architecture, project state, task board, and planning docs

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: production drivers were absent from preflight/status reporting.
- Gaps: admin could not see why Coolify/archive were ready or blocked.
- Inconsistencies: none blocking.
- Architecture constraints: no deploy secret exposure, no runtime file mutation,
  fail-closed apply behavior.

### 2. Select One Priority Task
- Selected task: production driver preflight status.
- Priority rationale: preflight visibility must exist before real apply
  execution.
- Why other candidates were deferred: Coolify/archive apply execution requires
  deeper rollback and security evidence.

### 3. Plan Implementation
- Files or surfaces to modify: drivers, config, update status, admin settings,
  tests, docs.
- Logic: add `coolify` and `archive` drivers that report readiness but return
  `supports_apply=false`; expose driver options in admin.
- Edge cases: missing Coolify webhook, secret webhook URL leakage, missing
  archive paths, unwritable archive paths.

### 4. Execute Implementation
- Implementation notes: added Coolify/archive driver classes, driver option
  status payloads, admin preflight cards, driver select options, config keys,
  translations, and regression tests.

### 5. Verify and Test
- Validation performed:
  - `php artisan test --filter=SystemUpdateCheckCommandTest`
  - `php artisan test --filter=SettingsManagementTest`
  - `npm run lint`
- Result: all passed.

### 6. Self-Review
- Simpler option considered: document preflight only.
- Technical debt introduced: no.
- Scalability assessment: driver-specific preflight keeps future apply logic
  isolated.
- Refinements made: Coolify preflight never returns the configured webhook URL.

### 7. Update Documentation and Knowledge
- Docs updated: architecture map, update contract, project state, task board,
  execution plan, next commits, task record.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] Coolify preflight reports configured readiness without exposing webhook
  secrets.
- [x] Archive preflight fails closed when staging/release paths are missing.
- [x] Admin settings displays driver preflight status.
- [x] Production drivers remain preflight-only and cannot replace runtime code.

## Validation Evidence
- Tests:
  - `php artisan test --filter=SystemUpdateCheckCommandTest` passed.
  - `php artisan test --filter=SettingsManagementTest` passed.
  - `npm run lint` passed.
- High-risk checks: Coolify webhook URL/token is not included in status
  messages; archive missing paths fail closed; `supports_apply=false`.

## Reliability / Observability Evidence
- Critical user journey: operator can see driver readiness before enabling
  production apply.
- SLI: driver preflight status is available in `updateStatus.driver_options`.
- Smoke command: view admin settings or inspect `UpdateManager::getStatus()`.
- Rollback or disable path: production drivers do not mutate files yet.

## Security / Privacy Evidence
- Trust boundaries: admin browser receives readiness metadata, not deploy
  secrets.
- Secret handling: Coolify webhook config remains server-side.
- Fail-closed behavior: missing archive paths and missing Coolify webhook block
  readiness.
- Residual risk: real apply execution still needs dedicated tests and threat
  model.

## Architecture Evidence
- Fits approved architecture: yes.
- Mismatch discovered: no.
- Decision required from user: no.

## Result Report
- Task summary: added Coolify/archive preflight status and admin visibility
  while keeping production apply execution disabled.
- How tested: targeted PHPUnit tests and frontend lint.
- What is incomplete: Coolify/archive apply execution, rollback and integrity
  checks.
- Next steps: add Coolify apply fake/test path before any live webhook trigger.
