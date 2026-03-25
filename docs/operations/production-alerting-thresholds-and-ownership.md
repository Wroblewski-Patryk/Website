# Production Alerting Thresholds and Ownership Map

## Scope
This document defines baseline production alert thresholds, default ownership, and escalation paths for core runtime signals.

## Ownership Model
- Platform owner: runtime infrastructure, deployment health, and global SLO enforcement.
- Backend owner: API, queue workers, database-facing behavior, and scheduled jobs.
- Frontend owner: admin/public frontend runtime issues and client-side error spikes.
- Product owner (incident liaison): impact assessment and user communication.

## Alert Matrix
| Signal | Trigger Threshold | Severity | Primary Owner | Secondary Owner | Immediate Action |
| --- | --- | --- | --- | --- | --- |
| HTTP 5xx error rate | `>2%` for `5m` on public/admin traffic | SEV-1 | Platform owner | Backend owner | Start incident, assess rollback, freeze non-essential deploys |
| P95 request latency | `>1200ms` for `10m` | SEV-2 | Backend owner | Platform owner | Check slow query logs, queue lag, recent deploy deltas |
| Queue lag | `>120s` for `10m` | SEV-2 | Backend owner | Platform owner | Scale workers, inspect stuck jobs, review failed jobs |
| Failed jobs growth | `>20` new failed jobs in `15m` | SEV-2 | Backend owner | Platform owner | Identify failing job class and quarantine retry storms |
| Slow query events | `>15` events in `60m` | SEV-2 | Backend owner | Platform owner | Inspect offending SQL and lock/contention hotspots |
| Cache probe failure | any failure on consecutive probes (`2x`) | SEV-1 | Platform owner | Backend owner | Verify cache backend reachability and failover path |
| Frontend runtime errors | `>30` events in `10m` per release/environment | SEV-2 | Frontend owner | Platform owner | Correlate with release, isolate route/component scope |
| Health check failure (`ops:health-check`) | any subsystem `ok=false` in production schedule | SEV-1 | Platform owner | Backend owner | Validate DB/cache/queue availability and initiate incident |

## Source of Truth for Signals
- Error tracking: Sentry project (backend + frontend), environment-tagged.
- Operational metrics: `php artisan ops:metrics --json` snapshot output and scheduled log events.
- Readiness checks: `php artisan ops:health-check --json` and scheduled result.
- Runtime logs:
  - `Slow query detected`
  - `Route response budget exceeded`
  - `ops:metrics snapshot`

## Escalation Timers
- SEV-1:
  - Acknowledge within 5 minutes.
  - Incident commander assigned within 10 minutes.
  - Rollback/no-rollback decision within 20 minutes.
- SEV-2:
  - Acknowledge within 15 minutes.
  - Owner triage summary within 30 minutes.
  - Mitigation or downgrade plan within 60 minutes.
- SEV-3:
  - Triage during on-call shift and create backlog task with owner/date.

## Tuning Policy
- Thresholds are reviewed after each incident and at least monthly.
- Any threshold change requires:
  - reason for change,
  - expected false-positive/false-negative tradeoff,
  - rollback strategy for the alert rule.

