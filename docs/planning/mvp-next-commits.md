# MVP Next Commits

## NOW (max 3)
- [ ] SCL-056 Add token-scoped access model for headless read API
- [ ] SCL-060 Add media lifecycle policy (archive, retention, purge)
- [ ] SCL-062 Integrate backend+frontend error tracking platform

## NEXT
- [ ] SCL-063 Add core operational metrics (queue lag, slow queries, cache hit)
- [ ] SCL-064 Expand health checks for DB/cache/queue readiness
- [ ] SCL-065 Define production alerting thresholds and ownership map

## LATER
- [ ] Refill from backlog after NOW/NEXT completion

## Program Backlog
- Full backlog source: `docs/planning/scaling-backlog-65.md`
- Program roadmap: `docs/planning/scaling-roadmap.md`

## Refill Rules
- Pull from `docs/planning/scaling-backlog-65.md` when `NOW` is empty.
- Keep priority order (P0 -> P1 -> P2 -> P3) and dependencies intact.
- Keep tasks small and reversible (`<=10 files` per task).
