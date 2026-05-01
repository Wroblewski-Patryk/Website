# Task

## Header
- ID: FEA-015D
- Title: Add gated Coolify apply trigger test path
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-015C
- Priority: P1
- Coverage Ledger Rows: not applicable
- Iteration: 5
- Operation Mode: TESTER

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number.
- [x] The task is aligned with repository source-of-truth documents.

## Context
Coolify preflight existed, but apply execution needed a guarded path that could
be tested without hitting a live deployment target.

## Goal
Trigger the configured Coolify deployment webhook only when an explicit
environment kill switch enables it, while proving disabled mode sends no
request and secrets are not exposed in status output.

## Scope
- `app/Services/SystemUpdates/Drivers/CoolifyUpdateDriver.php`
- `app/Services/SystemUpdates/UpdateManager.php`
- `config/updates.php`
- `tests/Feature/SystemUpdateCheckCommandTest.php`
- architecture, reliability, project state, task board, and planning docs

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: Coolify driver could report readiness but could not exercise the
  deployment trigger contract.
- Gaps: no test proving enabled trigger behavior or disabled no-request
  behavior.
- Inconsistencies: none blocking.
- Architecture constraints: deploy secrets must stay server-side; unsupported
  or disabled apply must fail closed.

### 2. Select One Priority Task
- Selected task: gated Coolify apply trigger path.
- Priority rationale: Coolify is the safest first production apply target
  because rollback can use platform deployment history.
- Why other candidates were deferred: archive file replacement has higher local
  filesystem and rollback risk.

### 3. Plan Implementation
- Files or surfaces to modify: Coolify driver, update config, status/audit
  event selection, tests, docs.
- Logic: require configured webhook and `FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED`
  before sending a POST; keep disabled mode as `preflight_only`.
- Edge cases: disabled trigger, failed webhook, secret URL leakage, async deploy
  not yet confirmed.

### 4. Execute Implementation
- Implementation notes: added Coolify apply flag, server-side webhook POST,
  deployment-triggered status, operator instructions, and tests using
  `Http::fake`.

### 5. Verify and Test
- Validation performed:
  - `php artisan test --filter=SystemUpdateCheckCommandTest`
  - `php artisan test --filter=SettingsManagementTest`
  - `npm run lint`
- Result: all passed.

### 6. Self-Review
- Simpler option considered: make preflight only and defer trigger.
- Technical debt introduced: no.
- Scalability assessment: the trigger path is isolated in the Coolify driver
  and remains disabled unless explicitly enabled.
- Refinements made: deployment trigger does not mark the app as updated because
  Coolify deploy completion is asynchronous.

### 7. Update Documentation and Knowledge
- Docs updated: architecture map, update manager contract, reliability doc,
  project state, task board, execution plan, next commits, task record.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] Coolify webhook POST occurs only when apply is explicitly enabled.
- [x] Disabled Coolify apply sends no HTTP request.
- [x] Status messages do not expose the webhook token.
- [x] Triggered deployment records `deployment_triggered` without updating
  current version.

## Validation Evidence
- Tests:
  - `php artisan test --filter=SystemUpdateCheckCommandTest` passed.
  - `php artisan test --filter=SettingsManagementTest` passed.
  - `npm run lint` passed.
- High-risk checks: `Http::fake` prevents real deploy calls in tests; disabled
  config sends no request; webhook token is not returned in status strings.

## Reliability / Observability Evidence
- Critical user journey: operator can trigger Coolify deploy through server
  path only when enabled.
- SLI: `apply_status=deployment_triggered` after accepted webhook response.
- Smoke command: `php artisan updates:apply --force`.
- Rollback or disable path: set `FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED=false`;
  rollback through Coolify deployment history.

## Security / Privacy Evidence
- Trust boundaries: browser triggers admin backend; backend owns webhook secret.
- Secret handling: webhook URL remains config-only and is not returned in
  status payloads or operator messages.
- Fail-closed behavior: apply flag false sends no request.
- Residual risk: live Coolify rollout still needs staging smoke and
  post-deploy health confirmation.

## Architecture Evidence
- Fits approved architecture: yes.
- Mismatch discovered: no.
- Decision required from user: no.

## Result Report
- Task summary: added gated Coolify webhook trigger and regression tests.
- How tested: targeted PHPUnit tests and frontend lint.
- What is incomplete: post-deploy health confirmation, live rollout evidence,
  archive apply, Docker/Git driver decisions.
- Next steps: add post-deploy health/status follow-up for Coolify-triggered
  deployments.
