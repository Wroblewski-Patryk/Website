## Summary
- Scope:
- Risk level: Low / Medium / High

## Automated Checks
- [ ] `php artisan test`
- [ ] `composer analyse`
- [ ] `npm run lint`
- [ ] `npm run format:check`
- [ ] Stack-specific high-risk checks
- [ ] Build completed without errors
- Commands run:
  - 
- Results:
  - 

## Manual Smoke (Browser-Driven)
- [ ] Admin route checked if admin behavior changed
- [ ] Localized public route checked if routing/content behavior changed
- [ ] i18n scan run if translation keys or copy changed
- [ ] I executed the relevant user or operator flow for this change.
- [ ] I covered at least one negative or validation scenario.
- [ ] I verified the feature uses real data/API/service paths, not mocks.
- [ ] I verified the feature works after refresh, reload, or restart where
  applicable.
- [ ] I captured evidence (screenshot and DOM or log where needed).
- [ ] If this was a canonical-visual UI task, I compared browser screenshots to
  the approved reference and documented remaining mismatches.

Flows executed:
- 

## Evidence Links
- Artifact folder or location:
- Screenshots:
- Canonical comparison notes:
- DOM snapshots:
- Logs:

## Context Updated
- [ ] `.codex/context/TASK_BOARD.md`
- [ ] `.codex/context/PROJECT_STATE.md`
- [ ] `docs/planning/*` if priorities or sequencing changed
- [ ] Project docs or ADRs (if used):

## Deployment Impact
- Deploy impact: none / low / medium / high
- [ ] `DEPLOYMENT_GATE.md` reviewed.
- [ ] `docs/operations/post-deploy-smoke.md` reviewed or updated
- [ ] `docs/operations/rollback-and-recovery.md` reviewed or updated
- Env or secret changes:
- Runtime services affected:

## Rollback Plan
- 

## Production Hardening Checklist

- [ ] `DEFINITION_OF_DONE.md` satisfied.
- [ ] `INTEGRATION_CHECKLIST.md` satisfied where applicable.
- [ ] `NO_TEMPORARY_SOLUTIONS.md` satisfied.
- [ ] `DEPLOYMENT_GATE.md` reviewed for release/deploy impact.
- [ ] `docs/security/secure-development-lifecycle.md` reviewed when security,
  permissions, secrets, AI, integrations, or user data were touched.
- [ ] `docs/operations/service-reliability-and-observability.md` reviewed when
  deployable services or critical journeys were touched.
- [ ] No mock, placeholder, fake, or temporary path remains.
- [ ] Feature uses real data/API/service paths.
- [ ] Feature works after refresh, reload, or restart where applicable.
- [ ] Result report includes what was done, files changed, how tested, what is incomplete, next steps, and decisions made.

## AI Safety Checklist

- [ ] Not applicable.
- [ ] `AI_TESTING_PROTOCOL.md` scenarios executed.
- [ ] Prompt injection checks passed.
- [ ] Data leakage checks passed.
- [ ] Unauthorized access checks passed.
- [ ] AI red-team findings resolved or explicitly accepted.
