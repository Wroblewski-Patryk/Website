# Task

## Header
- ID: FEA-015K
- Title: Defer Docker/Git runtime drivers from System Update Manager v1
- Task Type: design
- Current Stage: verification
- Status: DONE
- Owner: Ops/Release
- Depends on: FEA-015J
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
System Update Manager originally listed Coolify, archive, Docker, Git, and
manual directions. Coolify and archive now have guarded v1 paths, while Docker
and Git runtime drivers would require broader host control, secret handling,
process restart, migration, health, and rollback contracts.

## Goal
Close the Docker/Git v1 direction so the update manager does not grow unsafe
placeholder runtime drivers.

## Scope
- Close an architecture decision in `docs/planning/open-decisions.md`.
- Update System Update Manager architecture docs.
- Link Docker deployment v1 behavior to Coolify/platform rollout.
- Update rollback docs, task board, project state, and task evidence.

## Implementation Plan
1. Review current update manager contract and open decisions.
2. Add DEC-009 to explicitly defer Docker/Git runtime drivers from v1.
3. Update architecture to remove Docker/Git from the active v1 driver set.
4. Record platform/operator ownership for Docker/Git deployment rollback.
5. Update planning and context artifacts.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: Docker/Git remained listed as driver ideas without v1 safety
  contracts.
- Gaps: no explicit decision prevented placeholder driver classes.
- Inconsistencies: task board required Docker/Git direction to be decided or
  deferred.
- Architecture constraints: no new runtime self-updaters without dedicated
  contracts.

### 2. Select One Priority Task
- Selected task: FEA-015K Docker/Git v1 deferral.
- Priority rationale: closes one of FEA-015's explicit v1 checklist gaps.
- Why other candidates were deferred: Coolify live evidence needs environment
  access; archive extraction needs ZIP support.

### 3. Plan Implementation
- Files or surfaces to modify: open decisions, architecture contract, Coolify
  rollout runbook, rollback docs, project state, task board, planning docs.
- Logic: Docker/Git are deployment modes, not runtime drivers in v1.
- Edge cases: avoid placeholder classes and avoid claiming Docker/Git support.

### 4. Execute Implementation
- Implementation notes: added DEC-009 and updated docs to route Docker/Git
  through platform/operator workflows for v1.

### 5. Verify and Test
- Validation performed: documentation whitespace check.
- Result: decision docs and architecture references are synchronized.

### 6. Self-Review
- Simpler option considered: leave Docker/Git as future bullets only.
- Technical debt introduced: no.
- Scalability assessment: dedicated v2 contracts can add Docker/Git later
  without compromising v1 safety.
- Refinements made: documented that no placeholder Docker/Git driver classes
  should be added in v1.

### 7. Update Documentation and Knowledge
- Docs updated: yes.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] Docker/Git runtime driver direction is closed.
- [x] Docker/Git are deferred from v1.
- [x] Architecture docs no longer present Docker/Git as active v1 drivers.
- [x] Rollback ownership for Docker/Git deployments remains platform/operator.

## Success Signal
- User or operator problem: v1 update scope must avoid unsafe host mutation.
- Expected product or reliability outcome: System Update Manager v1 remains
  bounded to guarded Coolify/archive/manual paths.
- How success will be observed: DEC-009 records the v1 decision and no
  Docker/Git driver classes exist.
- Post-launch learning needed: yes.

## Deliverable For This Stage
Verified architecture decision and documentation update.

## Constraints
- Do not create Docker/Git driver classes.
- Do not claim runtime support.
- Keep production update application fail-closed.

## Definition of Done
- [x] Code builds without errors: not applicable, docs-only.
- [x] Feature works manually through the real operator path: not applicable.
- [x] No mock, placeholder, fake, or temporary path remains in production
  behavior.
- [x] Changes are documented in the relevant source of truth.
- [x] Behavior is reproducible from the evidence recorded below.
- [x] Reliability, security, and rollback evidence are recorded.
- [x] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Validation Evidence
- Tests: not applicable for docs-only decision slice.
- Manual checks: reviewed architecture and task board references.
- Screenshots/logs: not applicable.
- High-risk checks: no Docker/Git runtime driver code was added.
- Coverage ledger updated: not applicable.
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes.
- Real API/service path used: not applicable.
- Endpoint and client contract match: not applicable.
- DB schema and migrations verified: not applicable.
- Loading state verified: not applicable.
- Error state verified: not applicable.
- Refresh/restart behavior verified: not applicable.
- Regression check performed: documentation whitespace check.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: yes.
- Critical user journey: avoid unsafe runtime self-updaters in v1.
- SLI: not applicable for docs-only slice.
- SLO: not applicable.
- Error budget posture: healthy.
- Health/readiness check: deferred to future driver contracts.
- Logs, dashboard, or alert route: not applicable.
- Smoke command or manual smoke: not applicable.
- Rollback or disable path: Docker/Git rollback remains platform/operator
  owned until dedicated contracts exist.

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes.
- Data classification: deployment secrets and host control.
- Trust boundaries: application runtime to host/container/Git process.
- Permission or ownership checks: deferred to future contracts.
- Abuse cases: arbitrary Git checkout, unsafe image pull, secret leakage,
  incomplete migration rollback.
- Secret handling: no new secrets introduced.
- Security tests or scans: not applicable.
- Fail-closed behavior: no runtime Docker/Git driver exists in v1.
- Residual risk: future v2 contracts required if Docker/Git runtime updates are
  needed.

## Architecture Evidence
- Architecture source reviewed:
  `docs/architecture/system-update-manager-contract.md`.
- Fits approved architecture: yes.
- Mismatch discovered: no.
- Decision required from user: no.
- Approval reference if architecture changed: user requested v1 continuation on
  2026-05-01.
- Follow-up architecture doc updates: none.

## Deployment / Ops Evidence
- Deploy impact: medium.
- Env or secret changes: none.
- Health-check impact: none.
- Smoke steps updated: not applicable.
- Rollback note: Docker/Git rollback remains platform/operator-owned.
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
- [x] Deployment gate evidence is attached.
- [x] Definition of Done evidence is attached.
- [x] Relevant validations were run or scoped out.
- [x] Docs or context were updated if repository truth changed.
- [x] Learning journal was updated if a recurring pitfall was confirmed.

## Result Report
- Task summary: Docker/Git runtime update drivers are deferred from v1.
- Files changed: `docs/planning/open-decisions.md`,
  `docs/architecture/system-update-manager-contract.md`,
  `docs/architecture/current-implementation-map.md`,
  `docs/operations/coolify-update-rollout-runbook.md`,
  `docs/operations/rollback-and-recovery.md`, and planning/context docs.
- How tested: `git diff --check`.
- What is incomplete: future Docker/Git contracts if v2 needs runtime drivers.
- Next steps: resolve remaining v1 blockers: ZIP extraction validation or
  Coolify staging/live rollout evidence.
- Decisions made: no placeholder Docker/Git driver classes in v1.

## Notes
This keeps v1 focused on guarded Coolify, archive verification, and manual
fallback behavior.
