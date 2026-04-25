# Architecture Documentation for Featherly

This folder is the canonical source of truth for how Featherly works.

Use these files when the question is:
- how the system is structured,
- which module or boundary owns data and behavior,
- which invariants are fail-closed,
- how admin/public routing and localization contracts must behave.

Do not use this folder for:
- execution wave sequencing,
- temporary task logs,
- closure notes.

Those belong in:
- `docs/planning/`
- `docs/modules/`
- `docs/operations/`

## Reading Order
1. `architecture-source-of-truth.md`
2. `system-architecture.md`
3. `tech-stack.md`
4. `modules.md`
5. task-relevant architecture contracts

## Architecture Rules
- One file should have one clear responsibility.
- Resolved architecture decisions belong here, not only in planning notes.
- Module docs may explain implementation details, but do not override this
  folder.
