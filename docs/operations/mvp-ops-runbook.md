# MVP Ops Runbook

## Service Start and Health
- Start core services (app, queue worker, scheduler, cache) with environment-specific commands.
- Verify app health endpoint and database/cache connectivity.
- Verify queue worker is consuming jobs.

## Incident Triage
- Capture timestamp, environment, and first failing symptom.
- Check recent deploy/change log.
- Inspect application logs, queue failures, and alert timeline.
- Classify severity (`SEV-1`/`SEV-2`/`SEV-3`) and assign incident owner.

## Backup and Restore
- Confirm latest backup timestamp before risky operations.
- Validate restore procedure in non-production periodically.
- For data incidents, prefer point-in-time recovery with explicit data-loss window communication.

## Release and Rollback
### Pre-release
- Complete `docs/operations/release-candidate-checklist.md`.
- Confirm migration strategy and rollback path.

### Release steps
- Deploy application artifact.
- Run migrations (if any).
- Refresh caches/config where needed.
- Execute smoke checks on critical user journeys.

### Rollback triggers
- Sustained 5xx increase above alert threshold.
- Critical journey broken (auth, content save, public page render).
- Migration-related data integrity risk detected.

### Rollback steps
- Re-deploy previous stable artifact/tag.
- Run documented migration rollback or compensating script.
- Clear/restore cache keys impacted by release.
- Re-run smoke checks and confirm service recovery.

## Escalation
- `SEV-1`: immediate paging, engineering + product + incident owner.
- `SEV-2`: engineering lead + module owner within agreed SLA.
- `SEV-3`: triage in normal on-call workflow with backlog follow-up.
