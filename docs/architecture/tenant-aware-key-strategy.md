# Tenant-Aware Key Strategy (Cache, Session, Queue)

## Purpose
Establish a consistent key-naming and isolation strategy for multi-tenant readiness, so no cross-tenant cache/session/queue collisions can occur.

## Core Rule
All shared infrastructure keys must be prefixed with tenant scope:
- `tenant:{tenant_id}:{domain}:{key}`

Example:
- `tenant:42:cache:page:home:en`
- `tenant:42:session:user:153`
- `tenant:42:queue:publish:batch:2026-03-25T12:00`

## Cache Strategy
- Include tenant identifier in every cache key.
- Include locale where content is localized.
- Include version hash for schema-sensitive payloads.

Recommended shape:
- `tenant:{tenant_id}:cache:{bounded_context}:{resource}:{locale?}:{version}`

## Session Strategy
- Bind authenticated session context to tenant.
- Invalidate session when tenant switches without explicit re-auth.
- Prevent reuse of session token across tenant boundaries.

Recommended shape:
- `tenant:{tenant_id}:session:{session_id}`

## Queue Strategy
- Tag jobs with tenant id in payload metadata.
- Use tenant-specific queue names only where operationally justified.
- Ensure retries/dead-letter handling preserve tenant context.

Recommended metadata fields:
- `tenant_id`
- `tenant_slug`
- `trace_id`

## Lock/Mutex Strategy
- Distributed locks must be tenant-scoped to avoid global contention.

Recommended shape:
- `tenant:{tenant_id}:lock:{operation}:{resource_id}`

## Observability Contract
- Logs must include `tenant_id` and `tenant_slug` when available.
- Metrics should support per-tenant filters and global rollups.
- Alerting should include tenant context in incident payload.

## Migration Plan
1. Introduce key builder utilities (single source of truth).
2. Add tenant prefixing to new keys first.
3. Migrate existing hot paths with dual-read fallback window.
4. Remove legacy non-tenant keys after stabilization period.

## Validation Checklist
- No unscoped keys in cache/session/queue adapters.
- Integration tests for tenant A/B isolation.
- Background jobs verify tenant context before processing.
- Rollback path for key format migration is documented.
