# Commit and Task Naming Standard

## Goal
Keep change history searchable, consistent, and easy to trace to roadmap tasks.

## Task ID Format
- Use stable IDs in format: `SCL-0XX`.
- One logical task per commit where possible.
- If a task needs multiple commits, keep all commits prefixed with the same task ID.

## Commit Message Format
- Preferred: `SCL-0XX: short action summary`
- Examples:
  - `SCL-012: add transaction boundaries for admin content writes`
  - `SCL-026: reduce global Inertia payload footprint`

## Summary Rules
- Use imperative voice (`add`, `refactor`, `harden`, `remove`).
- Keep summary concise (around 5-10 words).
- Mention the real change, not generic labels (`update files`, `misc fixes`).

## Optional Scope Suffix
- Allowed when useful: `SCL-0XX(scope): short action summary`
- Example: `SCL-011(admin): migrate post validation to FormRequest`

## Pull Request Title
- Prefer the same style as commit messages:
  - `SCL-0XX: short action summary`
- If PR contains multiple related tasks:
  - `SCL-0XX/SCL-0YY: short shared outcome`

## Prohibited Patterns
- `WIP`, `fix`, `changes`, `update` without context.
- Missing task ID for planned roadmap work.
- Combining unrelated tasks in one commit.
