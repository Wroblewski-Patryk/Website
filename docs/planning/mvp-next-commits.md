# MVP Next Commits

## NOW (max 3)
- [ ] SCL-027 Add query profiling and remove N+1 in public render paths
- [ ] SCL-014 Standardize API response envelopes for admin endpoints
- [ ] SCL-061 Add request correlation IDs in logs

## NEXT
- [ ] SCL-028 Index taxonomy-heavy paths (`module`, `type`, `order`)
- [ ] SCL-029 Index revisions by morph columns and timestamp
- [ ] SCL-024 Add cache key versioning strategy for global datasets

## LATER
- [ ] SCL-039 Remove locale hardcodes and use active language source everywhere
- [ ] SCL-049 Add revision diff view (content comparison)
- [ ] SCL-061 Add request correlation IDs in logs

## Program Backlog
- Full backlog source: `docs/planning/scaling-backlog-65.md`
- Program roadmap: `docs/planning/scaling-roadmap.md`

## Refill Rules
- Pull from `docs/planning/scaling-backlog-65.md` when `NOW` is empty.
- Keep priority order (P0 -> P1 -> P2 -> P3) and dependencies intact.
- Keep tasks small and reversible (`<=10 files` per task).
