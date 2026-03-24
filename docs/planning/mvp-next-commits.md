# MVP Next Commits

## NOW (max 3)
- [ ] SCL-011 Migrate CRUD validation to dedicated FormRequest classes
- [ ] SCL-013 Add optimistic locking strategy for concurrent edits
- [ ] SCL-007 Introduce PR checklist for regression-safe delivery

## NEXT
- [ ] SCL-008 Introduce release checklist with rollback section
- [ ] SCL-027 Add query profiling and remove N+1 in public render paths
- [ ] SCL-036 Refine Vite chunk strategy to reduce oversized bundles

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
