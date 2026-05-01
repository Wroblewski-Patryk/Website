# MVP Next Commits

## NOW (max 3)
- [ ] Execute `FEA-018` project category compatibility retirement decision
- [ ] Resolve `FEA-015` production gate blocker by capturing Coolify
  staging/live rollout evidence in the target environment
- [ ] Execute `FEA-012` residual legacy docs normalization after project
  category direction is settled

## NEXT
- [ ] Audit remaining runtime persistence surfaces still depending on legacy
  `projects.category`
- [ ] Decide whether retiring the `projects.category` column needs a dedicated
  migration/backfill plan
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
