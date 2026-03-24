# JSON Search Strategy

## Goal
Define a scalable, DB-aware strategy for searching translated JSON fields (`title`, `slug`, `excerpt`, etc.) without changing current user-facing behavior during rollout.

## Current State
- Admin content listing currently searches locale-scoped JSON keys (for example `title->{locale}`).
- Some public routes use JSON extraction (`json_extract`) for exact slug lookup.
- Search behavior is functional but not unified across engines and not optimized for growth.

## Target Strategy
1. Normalize search entry points through reusable query helpers/scopes.
2. Keep locale-first semantics (`current_locale` first, then fallback locale).
3. Use engine-specific SQL fragments only behind one abstraction layer.
4. Preserve existing output and filters while improving query cost.

## Engine-Specific Plan
| Engine | Exact Match | Partial Match | Index Strategy |
|---|---|---|---|
| MySQL 8+ / MariaDB | `JSON_EXTRACT` with generated columns | `LIKE` on generated columns | Add generated columns + BTREE indexes for hot locales |
| PostgreSQL | `->>` operator | `ILIKE` on extracted text | Expression indexes on `(field->>'{locale}')` |
| SQLite | `json_extract` | `LIKE` on extracted text | Keep lightweight path; rely on pagination + selective filters |

## Rollout (No Regressions)
1. Add a small shared helper for locale-aware JSON search clauses.
2. Adopt helper in one admin controller path behind current query semantics.
3. Add integration tests that assert parity with existing results.
4. Expand to remaining content modules.
5. Add optional engine-specific index migrations where supported.

## Acceptance Criteria
- Same result set for existing search inputs in default locale.
- No route/API contract changes.
- No frontend/UI changes.
- Query plans do not regress on hot admin listing paths.
