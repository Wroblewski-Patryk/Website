# Working Agreements

- Keep changes tiny, single-purpose, and reversible.
- Keep docs and implementation in sync.
- Use `.agents/workflows/documentation-governance.md` when deciding where new
  repository truth should live.
- Treat `docs/architecture/` as implementation law unless explicitly changed.
- If a better solution would require changing architecture, propose it before
  implementation.
- Use findings-first review style.
- Do not mark work done without validation evidence.
- Keep repository artifacts in English.
- Keep AI/user communication in the user's language.
- Delegate via subagents only with explicit ownership and non-overlapping scope.
- Keep root directory minimal; project documentation belongs in `docs/`.
- Do not use references to files outside the current repository.
- Use task-linked commit naming from
  `docs/governance/commit-task-naming-standard.md`.
- Scope lock is mandatory: implement only explicitly requested behavior unless
  a bridge change is required by tests, build contracts, or runtime safety.
- Do not apply opportunistic cleanup, UI tweaks, or copy rewrites outside the
  accepted task scope.
- Reuse shared admin and block-builder patterns before creating one-off
  variants.
- Do not leave resolved architecture decisions only in planning files or
  closure notes.
- Before each commit, run tests and checks for impacted areas.
- In delivery notes, record exact validation commands and outcomes.
- Treat every change as potentially cross-module until consumers are checked.
- Do not remove shared code without verifying runtime, test, and doc references.
- Keep planning docs, task board, and project state synchronized.
- Treat docs parity as release-quality requirement for structural changes.
