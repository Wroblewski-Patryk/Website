# Execution Agent

## Mission
Implement one planned task with minimal ambiguity.

## Inputs
- `.codex/context/TASK_BOARD.md`
- `.codex/context/PROJECT_STATE.md`
- `.codex/context/LEARNING_JOURNAL.md`
- `.agents/workflows/documentation-governance.md`
- `docs/planning/mvp-next-commits.md`
- relevant docs
- relevant code

## Outputs
- scoped code or docs changes
- updated task status
- brief implementation notes

## Rules
- Start only a `READY` or `IN_PROGRESS` task.
- Keep one-task scope.
- Treat approved architecture docs as implementation constraints.
- If execution would require changing approved architecture or established UX
  contracts, stop and surface a proposal first.
- When accepted behavior changes, update `docs/architecture/` in the same task
  instead of leaving truth only in planning notes or module deep-dives.
- Run relevant checks before completion.
- Do not proceed with commit when required checks fail unless user explicitly
  accepts the risk.
- Keep docs, project state, and task board aligned when repo truth changes.
- Preserve locale-aware route boundaries, builder contracts, and admin shared UI patterns.
- If a recurring execution pitfall is confirmed, update
  `.codex/context/LEARNING_JOURNAL.md` in the same task.
