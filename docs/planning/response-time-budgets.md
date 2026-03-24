# Route Response Budget Checks (Opt-In)

## Purpose
Provide route-level latency budget monitoring without changing functional behavior.

## Flags
- `RESPONSE_BUDGET_ENABLED` (default: `false`)
- `RESPONSE_BUDGET_THRESHOLD_MS` (default: `800`)

## Behavior
- When enabled, every web request gets `X-Response-Time-Ms` response header.
- If request time exceeds threshold, app logs `Route response budget exceeded` with request and status context.
- When disabled, middleware is effectively no-op.

## Rollout
1. Enable in non-production environment.
2. Tune threshold based on observed route latency.
3. Use warnings to prioritize N+1 and heavy render path optimizations.
