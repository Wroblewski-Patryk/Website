# Query Profiling (Opt-In)

## Purpose
Provide a low-risk way to detect slow SQL queries while keeping default runtime behavior unchanged.

## Flags
- `DB_QUERY_PROFILING_ENABLED` (`false` by default)
- `DB_SLOW_QUERY_THRESHOLD_MS` (`75` by default)

## Behavior
- When disabled, no query listener is registered.
- When enabled, queries slower than the configured threshold are logged as warnings.
- Log payload includes SQL, bindings, execution time, connection, and request context (`method`, `path`, `request_id`) when available.

## Usage
1. Set `DB_QUERY_PROFILING_ENABLED=true`.
2. Optionally tune `DB_SLOW_QUERY_THRESHOLD_MS`.
3. Reproduce target page flows and inspect application logs.

## Notes
- This is profiling instrumentation only and does not alter query results.
- Use alongside targeted N+1 fixes in public render paths (`SCL-027`).
