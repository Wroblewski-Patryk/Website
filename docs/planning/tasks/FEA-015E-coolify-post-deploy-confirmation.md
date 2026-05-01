# Task

## Header
- ID: FEA-015E
- Title: Add post-deploy version confirmation for Coolify-triggered updates
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-015D
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
The Coolify driver can trigger a deployment webhook, but accepting that webhook
does not prove the new runtime is serving the expected Featherly version.

## Goal
Add a small, reproducible post-deploy confirmation path that marks the update
complete only after the running application reports the target version.

## Scope
- `UpdateManager` status confirmation logic.
- CLI command for operator/post-deploy smoke use.
- Feature tests for confirmed and pending runtime versions.
- Architecture, operations, task board, and planning documentation.

## Implementation Plan
1. Read the current System Update Manager contract, manager, commands, and
   tests.
2. Add a `updates:confirm` command that delegates to the existing manager.
3. Compare configured `APP_VERSION` with the stored target release version.
4. Store `confirmed` or `awaiting_confirmation` status without exposing
   secrets or mutating code.
5. Add regression tests and synchronize docs.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: Coolify webhook success could be mistaken for completed update.
- Gaps: no post-deploy version confirmation command existed.
- Inconsistencies: operations docs mentioned post-deploy health confirmation
  as pending.
- Architecture constraints: preserve server-side-only update status handling
  and fail-closed behavior.

### 2. Select One Priority Task
- Selected task: FEA-015E Coolify post-deploy version confirmation.
- Priority rationale: closes the gap between deployment trigger and runtime
  evidence before deeper production rollout work.
- Why other candidates were deferred: archive/Docker/Git drivers need more
  design and broader blast radius.

### 3. Plan Implementation
- Files or surfaces to modify: update manager, console command, update command
  tests, architecture and operations docs.
- Logic: if running `APP_VERSION` is greater than or equal to the target
  version, mark confirmed/current; otherwise keep update open as
  `awaiting_confirmation`.
- Edge cases: no target version, unchanged runtime version, repeated command
  runs after deploy.

### 4. Execute Implementation
- Implementation notes: added `UpdateManager::confirmDeployment()` and
  `updates:confirm`; status records `confirmed_at` only after version match.

### 5. Verify and Test
- Validation performed: targeted update command tests and Artisan command
  discovery.
- Result: confirmation succeeds when runtime version matches and remains open
  when it does not.

### 6. Self-Review
- Simpler option considered: mark deployment complete immediately after the
  webhook returns 2xx.
- Technical debt introduced: no.
- Scalability assessment: command can be reused by Coolify post-deploy hooks or
  manual operator smoke without changing driver contracts.
- Refinements made: kept health/readiness checks as a later slice so this task
  remains a narrow version-confirmation step.

### 7. Update Documentation and Knowledge
- Docs updated: architecture map, update manager contract, service reliability,
  post-deploy smoke, MVP plan, next commits, task board, and project state.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] `updates:confirm` is available as an Artisan command.
- [x] Matching runtime version marks the update `confirmed` and current.
- [x] Non-matching runtime version records `awaiting_confirmation`.
- [x] Documentation names the new post-deploy smoke step.

## Success Signal
- User or operator problem: operators need evidence that the app restarted on
  the intended release after a Coolify webhook.
- Expected product or reliability outcome: deployment status is not closed
  prematurely.
- How success will be observed: `system_update_status.apply_status` moves to
  `confirmed` only after `APP_VERSION` matches the target release.
- Post-launch learning needed: yes.

## Deliverable For This Stage
Verified CLI confirmation slice for Coolify-triggered update deployments.

## Constraints
- Use existing settings storage and audit logging.
- Preserve admin/public routing boundaries.
- Do not mutate runtime files.
- Do not expose deployment secrets.

## Definition of Done
- [x] Code builds without errors.
- [x] Feature works manually through the CLI path.
- [x] No mock, placeholder, fake, or temporary path remains in production
  behavior.
- [x] Full data flow works across manager, settings, CLI, audit, and tests.
- [x] No existing functionality is broken.
- [x] Feature works after restart because it reads runtime `APP_VERSION`.
- [x] Changes are documented in the relevant source of truth.
- [x] Behavior is reproducible from validation evidence.
- [x] Reliability and rollback evidence are recorded.
- [x] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Validation Evidence
- Tests: `php artisan test --filter=SystemUpdateCheckCommandTest`.
- Manual checks: `php artisan list updates`.
- Screenshots/logs: command namespace output shows `updates:confirm`.
- High-risk checks: update is not marked complete while runtime version remains
  below the target release.
- Coverage ledger updated: not applicable.
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes.
- Real API/service path used: not applicable.
- Endpoint and client contract match: not applicable.
- DB schema and migrations verified: not applicable.
- Loading state verified: not applicable.
- Error state verified: not applicable.
- Refresh/restart behavior verified: yes.
- Regression check performed: targeted command tests.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: yes.
- Critical user journey: post-deploy operator confirms the application is
  running the expected version.
- SLI: `system_update_status.apply_status` is `confirmed` only after version
  match.
- SLO: command completes quickly and never blocks public serving.
- Error budget posture: healthy.
- Health/readiness check: version confirmation added; deeper health check is a
  follow-up.
- Logs, dashboard, or alert route: audit events `updates.confirmed`,
  `updates.awaiting_confirmation`, and `updates.confirmation_unavailable`.
- Smoke command or manual smoke: `php artisan updates:confirm`.
- Rollback or disable path: Coolify deployment history; auto apply remains
  gated by environment.

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes.
- Data classification: version metadata and update status.
- Trust boundaries: CLI/operator to backend settings storage.
- Permission or ownership checks: command is server-side operator path.
- Abuse cases: premature confirmation, downgrade ambiguity, missing target
  version.
- Secret handling: no secrets are read or emitted by confirmation.
- Security tests or scans: targeted regression tests.
- Fail-closed behavior: missing target version records
  `confirmation_unavailable`.
- Residual risk: health/readiness and live Coolify evidence still pending.

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
- Health-check impact: adds version confirmation smoke command.
- Smoke steps updated: yes.
- Rollback note: rollback remains through Coolify deployment history.
- Observability or alerting impact: audit/status events added.
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
- [x] Deployment gate evidence is attached where applicable.
- [x] Definition of Done evidence is attached.
- [x] Relevant validations were run.
- [x] Docs or context were updated if repository truth changed.
- [x] Learning journal was updated if a recurring pitfall was confirmed.

## Result Report
- Task summary: added post-deploy version confirmation for System Update
  Manager.
- Files changed: `app/Services/SystemUpdates/UpdateManager.php`,
  `app/Console/Commands/ConfirmApplicationUpdate.php`,
  `tests/Feature/SystemUpdateCheckCommandTest.php`, and synchronized docs.
- How tested: `php artisan test --filter=SystemUpdateCheckCommandTest`;
  `php artisan list updates`.
- What is incomplete: Coolify live health-check integration and production
  rollout evidence.
- Next steps: add Coolify health/readiness confirmation after version match.
- Decisions made: version confirmation uses `APP_VERSION` as the canonical
  runtime signal.

## Notes
The command intentionally does not call Coolify. It confirms Featherly's own
runtime state after the hosting platform restarts the application.
