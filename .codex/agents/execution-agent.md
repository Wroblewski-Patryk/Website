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
- Before implementation, confirm the task contract includes the autonomous process self-audit, iteration number, operation mode, and all seven loop evidence sections.
- Start only a `READY` or `IN_PROGRESS` task.
- Keep one-task scope.
- Treat approved architecture docs as implementation constraints.
- If execution would require changing approved architecture or established UX
  contracts, stop and surface a proposal first.
- Use `.agents/workflows/user-collaboration.md` when ambiguity, blocker
  decisions, or user-authored interpretation notes affect the implementation
  path.
- Use `.agents/workflows/world-class-delivery.md` for substantial product,
  runtime, release, UX, security, or AI work.
- For UX/UI tasks with an approved screenshot, mockup, or canonical image,
  follow `docs/ux/canonical-visual-implementation-workflow.md`.
- For broad UX work, use `docs/ux/evidence-driven-ux-review.md` to turn
  clickthrough or screenshot evidence into focused implementation slices.
- When accepted behavior changes, update `docs/architecture/` in the same task
  instead of leaving truth only in planning notes or module deep-dives.
- Run relevant checks before completion.
- Do not proceed with commit when required checks fail unless user explicitly
  accepts the risk.
- Keep docs, project state, and task board aligned when repo truth changes.
- Preserve locale-aware route boundaries, builder contracts, and admin shared UI patterns.
- If a recurring execution pitfall is confirmed, update
  `.codex/context/LEARNING_JOURNAL.md` in the same task.

## Production Hardening Rules

- Always read existing code, architecture docs, task context, and relevant tests before writing new code.
- Never assume architecture; inspect it first.
- Modify only the minimal necessary scope declared in the task contract.
- Stop if proper implementation is not possible without placeholders, mock-only behavior, temporary fixes, or hidden bypasses.
- Deliver runtime features as vertical slices across UI, logic, API, DB, validation, error handling, and tests.
- Validate `DEFINITION_OF_DONE.md` before moving a task to `DONE`.
- Validate `INTEGRATION_CHECKLIST.md` before completing integrated work.
- Validate `AI_TESTING_PROTOCOL.md` before completing AI behavior.
- Validate `DEPLOYMENT_GATE.md` before release or deploy handoff.
- Validate `docs/security/secure-development-lifecycle.md` before completing
  security, permissions, secrets, AI, integrations, or user-data risk.
- Validate `docs/operations/service-reliability-and-observability.md` before
  completing deployable service, API, worker, scheduler, or critical-journey
  changes.

Completion requires a result report with what was done, files changed, how it was tested, what is incomplete, next steps, decisions made, and residual risks or assumptions. If runtime behavior changed, review deploy docs, smoke steps, and rollback notes in the same task.
