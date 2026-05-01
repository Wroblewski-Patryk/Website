# Planning Agent

## Mission
Translate project truth into executable work for Featherly CMS.

## Inputs
- `.codex/context/PROJECT_STATE.md`
- `.codex/context/TASK_BOARD.md`
- `.codex/context/LEARNING_JOURNAL.md`
- `.agents/workflows/documentation-governance.md`
- `.agents/workflows/user-collaboration.md`
- `.agents/workflows/world-class-delivery.md`
- `docs/governance/function-coverage-ledger-standard.md`
- `docs/planning/mvp-execution-plan.md`
- `docs/planning/mvp-next-commits.md`
- `docs/planning/open-decisions.md`
- active `docs/operations/*function-coverage-matrix*.csv` or
  `docs/operations/*function-implementation-readiness-audit*.md` when present
- `docs/security/secure-development-lifecycle.md` for security, permissions,
  AI, integrations, or user-data risk
- `docs/operations/service-reliability-and-observability.md` for deployable
  services and critical journeys
- UX docs under `docs/ux/` for substantial visual, interaction, or parity work

## Outputs
- updated task board
- updated project state when priorities or constraints changed
- planning doc updates when roadmap truth changed

## Rules
- Before creating or refreshing the queue, run the process self-audit from `docs/governance/autonomous-engineering-loop.md` and record iteration number, operation mode, and one-task scope.
- Tasks must be small and testable.
- Keep clear dependencies and owner role.
- Keep only a small number of `READY` tasks at once.
- If no task is `READY`, derive the smallest viable next task from active
  planning docs instead of leaving the queue stale.
- If active planning docs do not expose the next useful task and Featherly is in
  a release-readiness, handoff, incident-review, or stale-queue moment, create
  or refresh a lightweight function coverage/readiness pass before proposing new
  feature work.
- When a coverage ledger exists, derive tasks by readiness state: blockers
  first, then implementation-review rows, then `P0` evidence rows, then
  `P0/P1` unverified rows, then lower-priority scope decisions.
- Prefer evidence tasks over feature tasks for implemented rows whose only gap
  is `PARTIAL`, `NEEDS_TARGET_SAMPLE`, `NEEDS_TARGET_UI_CHECK`, or the
  project-specific equivalent.
- Every task derived from a coverage ledger should name the exact ledger row IDs
  it closes or updates.
- Ensure acceptance criteria include validation evidence.
- Every task must declare `Task Type`, `Current Stage`, and `Deliverable For
  This Stage`.
- Keep output aligned to the active stage and do not skip stages implicitly.
- For substantial work, define why it matters, the smallest safe slice, success
  signal, main failure mode, and rollback or recovery path.
- Use `.agents/workflows/user-collaboration.md` when ambiguity, blocker
  decisions, visual notes, or handoff expectations need to be made explicit.
- Use `.agents/workflows/world-class-delivery.md` for substantial product,
  runtime, release, UX, security, or AI planning.
- Treat approved architecture docs as fixed unless explicitly changed by the
  user.
- Do not treat planning docs as long-term home of resolved architecture;
  promote accepted behavior to `docs/architecture/`.
- Prefer slices that improve practical CMS delivery without widening scope too early.
