# Task

## Header
- ID: FEA-015F
- Title: Gate post-deploy confirmation on operational health checks
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-015E
- Priority: P1
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
`updates:confirm` could confirm a deployment based on version alone. Featherly
already had `ops:health-check` for DB/cache/queue readiness, but that logic was
embedded in the command and not reusable by the update manager.

## Goal
Reuse the existing operational health checks during update confirmation so a
Coolify-triggered deployment is complete only when version and readiness both
pass.

## Scope
- Extract operational health probes to a reusable service.
- Keep `ops:health-check` behavior compatible.
- Gate `updates:confirm` on the shared health payload.
- Add regression coverage for health failure.
- Update architecture, operations, and task context.

## Implementation Plan
1. Inspect the existing health command and update confirmation path.
2. Extract DB/cache/queue probes to `OperationalHealthChecker`.
3. Update `ops:health-check` to use the shared checker.
4. Update `UpdateManager::confirmDeployment()` to run the checker after version
   match.
5. Record `confirmation_health_failed` when health fails.
6. Add tests and synchronize docs.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: version confirmation alone could hide a broken DB/cache/queue state.
- Gaps: health probe logic was not reusable outside the command.
- Inconsistencies: operations docs required health confirmation, but
  `updates:confirm` only compared versions.
- Architecture constraints: reuse existing ops checks and avoid a parallel
  health system.

### 2. Select One Priority Task
- Selected task: FEA-015F update confirmation health gate.
- Priority rationale: closes the reliability gap before live rollout evidence.
- Why other candidates were deferred: archive/Git/Docker drivers have broader
  risk and should wait until Coolify confirmation is robust.

### 3. Plan Implementation
- Files or surfaces to modify: health command, new health checker service,
  update manager, update command tests, architecture and ops docs.
- Logic: only mark `confirmed` after version match and healthy DB/cache/queue
  probes; otherwise store `confirmation_health_failed`.
- Edge cases: matching version with unhealthy queue config, repeated
  confirmation after health recovers, existing health command output.

### 4. Execute Implementation
- Implementation notes: extracted `OperationalHealthChecker` and reused it in
  both `ops:health-check` and `updates:confirm`.

### 5. Verify and Test
- Validation performed: targeted update tests, settings tests, and live
  `ops:health-check --json` smoke.
- Result: healthy runtime confirms updates; unhealthy queue config blocks
  confirmation.

### 6. Self-Review
- Simpler option considered: duplicate the health checks inside `UpdateManager`.
- Technical debt introduced: no.
- Scalability assessment: the shared checker can be reused by future
  post-deploy hooks, dashboards, and release gates.
- Refinements made: kept health payload in `system_update_status` for operator
  visibility.

### 7. Update Documentation and Knowledge
- Docs updated: architecture map, update manager contract, service reliability,
  post-deploy smoke, MVP plan, next commits, task board, and project state.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] `ops:health-check` uses reusable health logic.
- [x] `updates:confirm` requires both matching version and passing health.
- [x] Health failure records `confirmation_health_failed`.
- [x] Tests cover health failure during update confirmation.

## Success Signal
- User or operator problem: a deploy can restart on the right version while
  still being operationally unhealthy.
- Expected product or reliability outcome: update confirmation reflects
  actual readiness, not just version metadata.
- How success will be observed: `system_update_status.health_status` is
  `passed` before `apply_status=confirmed`.
- Post-launch learning needed: yes.

## Deliverable For This Stage
Verified health-gated update confirmation slice.

## Constraints
- Reuse existing ops health semantics.
- Preserve admin/public boundaries.
- Do not expose secrets.
- Do not mutate code or deployment state.

## Definition of Done
- [x] Code builds without errors.
- [x] Feature works manually through the CLI/operator path.
- [x] No mock, placeholder, fake, or temporary path remains in production
  behavior.
- [x] Full data flow works across manager, settings, CLI, health checker,
  audit, and tests.
- [x] Backend error handling exists where applicable.
- [x] No existing functionality is broken.
- [x] Feature works after restart because it checks current runtime readiness.
- [x] Changes are documented in the relevant source of truth.
- [x] Behavior is reproducible from validation evidence.
- [x] Reliability and rollback evidence are recorded.
- [x] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Validation Evidence
- Tests: `php artisan test --filter=SystemUpdateCheckCommandTest`;
  `php artisan test --filter=SettingsManagementTest`.
- Manual checks: `php artisan ops:health-check --json`.
- Screenshots/logs: JSON health payload returned ok with database, cache, and
  queue checks.
- High-risk checks: matching runtime version with invalid queue config records
  `confirmation_health_failed`.
- Coverage ledger updated: not applicable.
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes.
- Real API/service path used: not applicable.
- Endpoint and client contract match: not applicable.
- DB schema and migrations verified: not applicable.
- Loading state verified: not applicable.
- Error state verified: yes.
- Refresh/restart behavior verified: yes.
- Regression check performed: targeted command tests.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: yes.
- Critical user journey: post-deploy operator confirms the app version and
  readiness after update.
- SLI: confirmed updates require passing DB/cache/queue readiness.
- SLO: confirmation command completes quickly and never blocks public serving.
- Error budget posture: healthy.
- Health/readiness check: shared `OperationalHealthChecker`.
- Logs, dashboard, or alert route: audit events
  `updates.confirmation_health_failed` and `updates.confirmed`.
- Smoke command or manual smoke: `php artisan updates:confirm` and
  `php artisan ops:health-check --json`.
- Rollback or disable path: Coolify deployment history; update apply remains
  env-gated.

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes.
- Data classification: operational readiness and version metadata.
- Trust boundaries: CLI/operator to backend health probes and settings storage.
- Permission or ownership checks: command is server-side operator path.
- Abuse cases: false-positive confirmation, leaking operational secrets,
  masking dependency failure.
- Secret handling: health payload records dependency status/details but no
  deployment secrets.
- Security tests or scans: targeted regression tests.
- Fail-closed behavior: unhealthy readiness blocks confirmation.
- Residual risk: live Coolify rollout evidence is still pending.

## Architecture Evidence
- Architecture source reviewed:
  `docs/architecture/system-update-manager-contract.md`.
- Fits approved architecture: yes.
- Mismatch discovered: no.
- Decision required from user: no.
- Approval reference if architecture changed: not applicable.
- Follow-up architecture doc updates: none.

## Deployment / Ops Evidence
- Deploy impact: low.
- Env or secret changes: none.
- Health-check impact: update confirmation now runs DB/cache/queue readiness.
- Smoke steps updated: yes.
- Rollback note: rollback remains through Coolify deployment history.
- Observability or alerting impact: health status is stored with update status.
- Staged rollout or feature flag: Coolify apply trigger remains env-gated.
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
- [x] Deployment gate evidence is attached.
- [x] Definition of Done evidence is attached.
- [x] Relevant validations were run.
- [x] Docs or context were updated if repository truth changed.
- [x] Learning journal was updated if a recurring pitfall was confirmed.

## Result Report
- Task summary: update confirmation now requires passing shared operational
  health checks as well as matching runtime version.
- Files changed: `app/Services/Operations/OperationalHealthChecker.php`,
  `app/Console/Commands/CheckOperationalHealth.php`,
  `app/Services/SystemUpdates/UpdateManager.php`,
  `tests/Feature/SystemUpdateCheckCommandTest.php`, and synchronized docs.
- How tested: targeted PHPUnit feature tests and `ops:health-check --json`.
- What is incomplete: Coolify live rollout evidence/runbook details.
- Next steps: document and test the live Coolify rollout evidence path before
  production-ready driver declaration.
- Decisions made: the existing DB/cache/queue readiness contract is the first
  update-confirmation health gate.

## Notes
This slice intentionally does not introduce external HTTP health probes. It
uses Featherly's existing internal readiness checks first.
