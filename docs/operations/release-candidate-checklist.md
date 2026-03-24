# Release Candidate Checklist

## Release Metadata
- [ ] release version/tag defined
- [ ] release owner assigned
- [ ] deployment window and timezone confirmed
- [ ] rollback owner assigned

## Scope Lock
- [ ] scope for this release is frozen
- [ ] known limits documented
- [ ] open follow-ups moved to next milestone

## Quality Gates (Required)
- [ ] `php artisan test`
- [ ] `composer analyse`
- [ ] `npm run build`
- [ ] `npm run lint`
- [ ] `npm run format:check`
- [ ] `php artisan migrate:fresh --seed --force` for migration-sensitive changes
- [ ] CI pipeline green on target branch

## Security and Dependency Gates
- [ ] `composer audit` (no blocking advisories)
- [ ] `npm audit --omit=dev` (no blocking advisories)
- [ ] secrets/config handling validated

## Operational Readiness
- [ ] runbook updated
- [ ] post-release monitoring plan prepared
- [ ] health checks verified
- [ ] alert routing verified

## Rollback Readiness (Mandatory)
- [ ] rollback trigger conditions documented
- [ ] rollback commands tested in staging or rehearsal environment
- [ ] DB migration rollback path documented (or explicit no-rollback + compensating plan)
- [ ] cache/config invalidation rollback notes prepared
- [ ] estimated rollback execution time recorded

## Sign-off
- [ ] product sign-off
- [ ] engineering sign-off
- [ ] release owner sign-off
