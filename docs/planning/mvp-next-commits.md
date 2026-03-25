# MVP Next Commits

## NOW (max 3)
- [ ] SCL-055 Prepare read-only headless API contract for public content
- [ ] SCL-052 Add scheduler observability for publish command execution
- [ ] SCL-057 Add content export endpoint for external consumption

## NEXT
- [ ] SCL-053 Add media deduplication by checksum
- [ ] SCL-054 Add safe replace workflow for duplicate assets
- [ ] SCL-058 Draft multi-tenant readiness architecture baseline

## LATER
- [ ] SCL-062 Integrate backend+frontend error tracking platform
- [ ] SCL-063 Add core operational metrics (queue lag, slow queries, cache hit)
- [ ] SCL-064 Expand health checks for DB/cache/queue readiness

## Program Backlog
- Full backlog source: `docs/planning/scaling-backlog-65.md`
- Program roadmap: `docs/planning/scaling-roadmap.md`

## Refill Rules
- Pull from `docs/planning/scaling-backlog-65.md` when `NOW` is empty.
- Keep priority order (P0 -> P1 -> P2 -> P3) and dependencies intact.
- Keep tasks small and reversible (`<=10 files` per task).
