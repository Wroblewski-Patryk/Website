# MVP Next Commits

## NOW (max 3)
- [ ] SCL-001 Add PHP static analysis baseline (PHPStan/Psalm) and CI gate
- [ ] SCL-003 Split CI into isolated jobs (tests/build/lint/audit/migrations)
- [ ] SCL-004 Add `composer audit` CI gate

## NEXT
- [ ] SCL-011 Migrate CRUD validation to dedicated FormRequest classes
- [ ] SCL-012 Add DB transactions for multi-step content writes
- [ ] SCL-026 Reduce heavy global Inertia shared payloads

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
