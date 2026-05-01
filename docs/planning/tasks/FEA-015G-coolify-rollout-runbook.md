# Task

## Header
- ID: FEA-015G
- Title: Add Coolify update rollout evidence runbook
- Task Type: release
- Current Stage: verification
- Status: DONE
- Owner: Ops/Release
- Depends on: FEA-015F
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
The Coolify driver now has a gated webhook trigger, version confirmation, and
operational health checks. The remaining gap is operational evidence: what an
operator must do and capture before calling the driver production-ready.

## Goal
Document the approved Coolify update rollout path from manifest check through
webhook trigger, version and health confirmation, smoke checks, evidence
capture, and rollback.

## Scope
- Add a dedicated Coolify update rollout runbook.
- Link the runbook from deployment, rollback, smoke, and architecture docs.
- Update task board, project state, MVP plan, and FEA-015 evidence.

## Implementation Plan
1. Review current Coolify deployment, rollback, smoke, and update manager docs.
2. Add a focused runbook with preconditions, commands, expected statuses,
   evidence requirements, failure handling, and rollback path.
3. Link the runbook from canonical ops and architecture docs.
4. Update planning/context artifacts.
5. Validate documentation changes with whitespace checks.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: Coolify trigger/confirmation had code evidence but no complete
  operator rollout evidence path.
- Gaps: production-ready criteria were implied across several docs.
- Inconsistencies: rollback docs did not name the Coolify deployment-history
  path for update driver failures.
- Architecture constraints: keep production auto-apply gated until evidence is
  captured.

### 2. Select One Priority Task
- Selected task: FEA-015G Coolify rollout runbook.
- Priority rationale: operational readiness must be explicit before any live
  driver declaration.
- Why other candidates were deferred: archive/Docker/Git implementation should
  not start before Coolify's rollout gate is clear.

### 3. Plan Implementation
- Files or surfaces to modify: operations docs, architecture references, task
  context.
- Logic: no runtime logic; document the evidence path and production-ready gate.
- Edge cases: webhook trigger failure, version mismatch, health failure, and
  rollback after defective release.

### 4. Execute Implementation
- Implementation notes: added
  `docs/operations/coolify-update-rollout-runbook.md` and linked it from
  canonical deployment, rollback, smoke, and architecture docs.

### 5. Verify and Test
- Validation performed: documentation whitespace check.
- Result: documentation changes passed `git diff --check` with only existing
  CRLF warnings.

### 6. Self-Review
- Simpler option considered: add a short paragraph to the deployment contract.
- Technical debt introduced: no.
- Scalability assessment: a separate runbook can collect staging/live evidence
  without bloating architecture docs.
- Refinements made: kept the production-ready gate explicit and left real
  staging evidence pending.

### 7. Update Documentation and Knowledge
- Docs updated: yes.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] Coolify rollout runbook exists.
- [x] Runbook includes preconditions, commands, evidence, failure handling, and
  rollback.
- [x] Deployment, rollback, smoke, and architecture docs link to the runbook.
- [x] Task board and project state reflect the remaining evidence gap.

## Success Signal
- User or operator problem: an update driver can look ready in code while the
  live rollout path remains ambiguous.
- Expected product or reliability outcome: production readiness requires
  captured evidence, not assumptions.
- How success will be observed: operators can follow one documented path and
  capture the required rollout artifacts.
- Post-launch learning needed: yes.

## Deliverable For This Stage
Verified documentation and evidence gate for Coolify update rollouts.

## Constraints
- Do not claim live Coolify evidence without running a real rollout.
- Preserve update-driver fail-closed status.
- Keep secrets out of docs and examples.

## Definition of Done
- [x] Code builds without errors: not applicable, docs-only.
- [x] Feature works manually through the real operator path: documented; live
  evidence pending.
- [x] No mock, placeholder, fake, or temporary path remains in production
  behavior.
- [x] Full data flow works across relevant docs.
- [x] No existing functionality is broken.
- [x] Changes are documented in the relevant source of truth.
- [x] Behavior is reproducible from the evidence recorded below.
- [x] Reliability, security, and rollback evidence expectations are recorded.
- [x] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Validation Evidence
- Tests: not applicable for docs-only slice.
- Manual checks: reviewed linked docs and command flow.
- Screenshots/logs: not applicable.
- High-risk checks: the runbook explicitly says production readiness requires
  captured staging/live evidence.
- Coverage ledger updated: not applicable.
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes.
- Real API/service path used: no.
- Endpoint and client contract match: not applicable.
- DB schema and migrations verified: not applicable.
- Loading state verified: not applicable.
- Error state verified: not applicable.
- Refresh/restart behavior verified: documented.
- Regression check performed: documentation whitespace check.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: yes.
- Critical user journey: operator safely rolls out and verifies a Coolify
  update.
- SLI: rollout evidence includes `updates:confirm` with version and health
  success.
- SLO: not applicable for docs-only slice.
- Error budget posture: healthy.
- Health/readiness check: `updates:confirm` and `ops:health-check --json`.
- Logs, dashboard, or alert route: Coolify deployment history and
  `system_update_status`.
- Smoke command or manual smoke: documented.
- Rollback or disable path: documented through Coolify deployment history and
  `FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED=false`.

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes.
- Data classification: deployment secrets, update status, rollout evidence.
- Trust boundaries: operator, application CLI, Coolify deployment platform.
- Permission or ownership checks: server-side operator path and admin settings.
- Abuse cases: secret leakage, false production readiness, unsafe repeated
  retries.
- Secret handling: runbook references secret names but does not include values.
- Security tests or scans: not applicable.
- Fail-closed behavior: runbook keeps Coolify apply disabled until evidence is
  captured.
- Residual risk: real staging/live evidence remains pending.

## Architecture Evidence
- Architecture source reviewed:
  `docs/architecture/system-update-manager-contract.md`.
- Fits approved architecture: yes.
- Mismatch discovered: no.
- Decision required from user: no.
- Approval reference if architecture changed: not applicable.
- Follow-up architecture doc updates: none.

## Deployment / Ops Evidence
- Deploy impact: medium.
- Env or secret changes: documents existing Coolify env requirements.
- Health-check impact: confirms existing `updates:confirm` and
  `ops:health-check --json` usage.
- Smoke steps updated: yes.
- Rollback note: Coolify deployment-history rollback documented.
- Observability or alerting impact: evidence capture uses
  `system_update_status` and Coolify deployment history.
- Staged rollout or feature flag: `FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED`.
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
- [x] Relevant validations were run or scoped out.
- [x] Docs or context were updated if repository truth changed.
- [x] Learning journal was updated if a recurring pitfall was confirmed.

## Result Report
- Task summary: added the Coolify update rollout runbook and linked it into
  canonical operations and architecture docs.
- Files changed: `docs/operations/coolify-update-rollout-runbook.md`,
  `docs/operations/coolify-vps-deployment-contract.md`,
  `docs/operations/rollback-and-recovery.md`,
  `docs/operations/post-deploy-smoke.md`,
  `docs/architecture/system-update-manager-contract.md`,
  `docs/architecture/current-implementation-map.md`, and planning/context docs.
- How tested: `git diff --check`.
- What is incomplete: captured staging/live rollout evidence.
- Next steps: capture Coolify rollout evidence using this runbook, or continue
  with archive driver integrity/staging design.
- Decisions made: Coolify production readiness requires evidence from the
  runbook, not just passing automated tests.

## Notes
This is intentionally an evidence/runbook slice. It does not mark Coolify
automatic updates production-ready by itself.
