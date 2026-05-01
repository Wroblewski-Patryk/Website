# Deployment Agent Checklist Template

## Mission
Deploy `Featherly CMS` to `<ENV>` using SHA `<SHA>` and return only after health and smoke validation.

## Scope
- Environment:
- Hosting target: Coolify on VPS | direct Docker Compose | other
- Change summary:
- Deployment risk:
- Runtime services affected:

## Inputs
1. Repo path
2. SHA or branch
3. Environment (`stage` or `production`)
4. Web URL
5. Admin URL
6. Service map or deployment topology

## Required Execution Order
1. Verify SHA and repo state.
2. Verify env variables and service routing.
3. Confirm backup, rollback, health, and smoke preconditions.
4. Run migrations.
5. Deploy app and frontend assets.
6. Run health or route smoke checks.
7. Validate one admin route and one public localized route.
8. Review startup logs, metrics, and runtime errors.
9. Report outcome.

## Pre-Deploy
- [ ] Release notes reviewed
- [ ] Required checks passed
- [ ] `DEPLOYMENT_GATE.md` has no hard blocks
- [ ] Config or secret changes reviewed
- [ ] Health endpoints confirmed
- [ ] Backup / restore preconditions reviewed
- [ ] Rollback path prepared
- [ ] Smoke checklist path confirmed

## Health And Smoke Commands
1. app health or equivalent runtime check
2. admin login surface reachable
3. one localized public route reachable
4. one critical admin flow smoke if release scope touched it

## Stop Conditions
1. migration failure
2. app not reachable
3. localized route or admin route smoke fails
4. asset/build mismatch blocks core UI

## Output Contract
1. Final status (`success`, `blocked`, `rolled_back`)
2. Deployed SHA
3. Passed or failed checks
4. Exact failing endpoint or error if blocked
5. Recommended next action

## Deployment Gate Evidence

- [ ] `DEPLOYMENT_GATE.md` has no hard blocks.
- [ ] Build passes without errors.
- [ ] Runtime startup logs have no blocking errors.
- [ ] API contracts match deployed clients.
- [ ] Required migrations are applied.
- [ ] Environment variables and secrets are configured.
- [ ] Rollback path is prepared and appropriate for the risk level.
- [ ] Observability or alert route is known for critical runtime paths.
- [ ] Feature flag, staged rollout, or disable path exists for high-risk
  changes.

## Post-Deploy Evidence
- Health:
- Smoke:
- Logs:
- Metrics:
- Manual journey verification:

## Rollback
- Trigger:
- Procedure:
- Verification after rollback:
