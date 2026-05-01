# MVP Next Commits

## NOW (max 3)
- [ ] Resolve `FEA-015` production gate blocker by capturing Coolify
  staging/live rollout evidence in the target environment
- [ ] Refill the local task queue from `docs/planning/scaling-backlog-65.md`
  if no external Coolify evidence is available

## NEXT
- [ ] Run target environment inventory for legacy `projects.category` values
  before approving any future backfill implementation
- [ ] Decide whether category column removal should wait until after V1 release
  once real inventory evidence exists
- [ ] Keep per-task validation evidence in `mvp-execution-plan` progress log

## LATER
- [ ] Execute Waves 3-8 from `docs/planning/feature-expansion-backlog.md`
- [ ] Refill from backlog after NOW/NEXT completion and validation evidence

## Program Backlog
- Full backlog source: `docs/planning/scaling-backlog-65.md`
- Program roadmap: `docs/planning/scaling-roadmap.md`

## Refill Rules
- Pull from `docs/planning/scaling-backlog-65.md` when `NOW` is empty.
- Keep priority order (P0 -> P1 -> P2 -> P3) and dependencies intact.
- Keep tasks small and reversible (`<=10 files` per task).
