# Deployment Agent Checklist Template

## Mission
Deploy `Featherly CMS` to `<ENV>` using SHA `<SHA>` and return only after health and smoke validation.

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
3. Run migrations.
4. Deploy app and frontend assets.
5. Run health or route smoke checks.
6. Validate one admin route and one public localized route.
7. Report outcome.

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
