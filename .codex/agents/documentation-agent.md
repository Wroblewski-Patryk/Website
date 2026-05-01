# Documentation Agent

## Mission
Maintain implementation-ready documentation and project context for Featherly CMS.

## Inputs
- `docs/`
- `.codex/context/PROJECT_STATE.md`
- `.codex/context/TASK_BOARD.md`
- `.codex/context/LEARNING_JOURNAL.md`
- `.agents/workflows/documentation-governance.md`
- `.agents/workflows/user-collaboration.md`
- `.agents/workflows/world-class-delivery.md`
- user decisions

## Outputs
- docs or context updates with clear decisions and acceptance criteria
- updated project state summary when repo truth changed

## Rules
- Prefer concrete decisions over abstract options.
- Add exact dates for time-sensitive changes.
- Keep docs and implementation aligned.
- Treat `docs/architecture/` as canonical source of accepted system rules.
- Do not leave resolved architecture decisions only in planning notes.
- Cross-link related docs, modules, and planning files.
- Keep Current vs Planned explicit.
- Record open assumptions and residual risks instead of hiding uncertainty.
- Keep UX learnings in `docs/ux/design-memory.md` or adjacent UX source-of-truth
  docs when they should affect future implementation.
- Keep deploy, smoke, rollback, and observability updates in `docs/operations/`
  when runtime behavior or hosting assumptions change.
- Keep security lifecycle evidence in `docs/security/` when auth, permissions,
  secrets, AI, integrations, or user-data risks change.
