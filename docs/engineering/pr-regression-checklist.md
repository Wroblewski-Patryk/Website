# PR Regression-Safe Checklist

Use this checklist before requesting review.

## Scope and Risk
- [ ] Task ID(s) included in PR title or description (for example `SCL-012`).
- [ ] Scope is a single logical change set.
- [ ] Change is reversible (rollback path identified).
- [ ] Risk level is stated (`Low`, `Medium`, `High`) with a short reason.

## Functional Safety
- [ ] No intentional user-facing behavior change unless explicitly planned.
- [ ] No intentional visual change unless explicitly planned.
- [ ] Existing critical flows still work for touched modules.
- [ ] Edge cases for touched logic are covered (validation, empty data, permissions).

## Validation Gates
- [ ] `php artisan test`
- [ ] `composer analyse`
- [ ] `npm run build`
- [ ] `npm run lint`
- [ ] `npm run format:check`
- [ ] Migration smoke when schema/data path is touched (`php artisan migrate:fresh --seed --force`)
- [ ] Security gates when dependency/workflow changes are included (`composer audit`, `npm audit --omit=dev`)

## Evidence and Documentation
- [ ] Commands and outcomes captured in PR description.
- [ ] Manual smoke journeys listed (if UI or runtime behavior touched).
- [ ] Evidence linked (screenshots/logs) for high-risk or UI-sensitive changes.
- [ ] Relevant docs updated (`docs/engineering/*`, module docs, roadmap/backlog status).

## Deployment and Rollback
- [ ] Backward compatibility considered (DB, cache, API contracts).
- [ ] Rollback plan is concrete and feasible.
- [ ] Post-merge follow-ups are listed (if any).
