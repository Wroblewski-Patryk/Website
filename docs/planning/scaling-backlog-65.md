# Scaling Backlog (65 Items)

Status legend: `TODO`, `IN_PROGRESS`, `DONE`, `BLOCKED`

## Phase 0 - Guardrails and Delivery Safety
- [x] SCL-001 (P0, DONE) Add PHP static analysis baseline (PHPStan/Psalm) and CI gate.
- [x] SCL-002 (P0, DONE) Add frontend lint/format validation in CI.
- [x] SCL-003 (P0, DONE) Split CI into isolated jobs (tests/build/lint/audit/migrations).
- [x] SCL-004 (P0, DONE) Add `composer audit` CI gate.
- [x] SCL-005 (P1, DONE) Add migration smoke workflow (`migrate:fresh --seed`).
- [x] SCL-006 (P1, DONE) Add bundle-size report artifact in CI.
- [x] SCL-007 (P1, DONE) Introduce PR checklist for regression-safe delivery.
- [x] SCL-008 (P1, DONE) Introduce release checklist with rollback section.
- [x] SCL-009 (P1, DONE) Define commit/task naming standard with IDs.
- [ ] SCL-010 (P2, TODO) Add architecture dependency checks (layer boundaries).

## Phase 1 - Core Stability
- [x] SCL-011 (P0, DONE) Migrate CRUD validation to dedicated FormRequest classes.
- [x] SCL-012 (P0, DONE) Add DB transactions for multi-step content writes.
- [x] SCL-013 (P0, DONE) Add optimistic locking strategy for concurrent edits.
- [x] SCL-014 (P1, DONE) Standardize API response envelopes for admin endpoints.
- [x] SCL-015 (P1, DONE) Add policy-based authorization for core content models.
- [x] SCL-016 (P1, DONE) Add audit logging for RBAC and settings changes.
- [x] SCL-017 (P1, DONE) Remove legacy dual-source role ambiguity (`users.role` vs Spatie).
- [x] SCL-018 (P1, DONE) Add status value constraints at DB level.
- [x] SCL-019 (P1, DONE) Enforce single default language invariant in DB.
- [ ] SCL-020 (P2, TODO) Add idempotency handling for duplicate-prone submissions.
- [x] SCL-021 (P1, DONE) Harden media upload validation (MIME sniff + file checks).
- [ ] SCL-022 (P2, TODO) Evaluate private media storage with signed access paths.
- [x] SCL-023 (P1, DONE) Normalize cache invalidation for shared Inertia data.
- [x] SCL-024 (P2, DONE) Add cache key versioning strategy for global datasets.
- [ ] SCL-025 (P2, TODO) Introduce domain services for repetitive controller workflows.

## Phase 2 - Performance and Throughput
- [x] SCL-026 (P0, DONE) Reduce heavy global Inertia shared payloads.
- [ ] SCL-027 (P1, IN_PROGRESS) Add query profiling and remove N+1 in public render paths.
- [x] SCL-028 (P1, DONE) Index taxonomy-heavy paths (`module`, `type`, `order`).
- [x] SCL-029 (P1, DONE) Index revisions by morph columns and timestamp.
- [x] SCL-030 (P1, DONE) Add search strategy for JSON-translated content.
- [x] SCL-031 (P1, DONE) Add pagination strategy for very large media collections.
- [x] SCL-032 (P1, DONE) Introduce cursor pagination where offset scaling hurts.
- [x] SCL-033 (P2, DONE) Define cache TTL matrix (what is forever vs expiring).
- [x] SCL-034 (P2, DONE) Add route-level response time budget checks.
- [x] SCL-035 (P2, DONE) Build per-module performance smoke script.
- [x] SCL-036 (P1, DONE) Refine Vite chunk strategy to reduce oversized bundles.
- [ ] SCL-037 (P2, TODO) Add frontend runtime memory/perf watch for block builder.
- [ ] SCL-038 (P2, TODO) Add virtualized rendering for large admin lists/tables.

## Phase 3 - i18n and SEO Hardening
- [ ] SCL-039 (P0, TODO) Remove locale hardcodes and use active language source everywhere.
- [ ] SCL-040 (P1, TODO) Standardize localized slug conflict policy.
- [ ] SCL-041 (P1, TODO) Add slug normalization and reserved-path safeguards.
- [ ] SCL-042 (P1, TODO) Improve canonical URL validation/normalization.
- [ ] SCL-043 (P1, TODO) Improve hreflang generation for nested and archive routes.
- [ ] SCL-044 (P1, TODO) Add i18n coverage dashboard from translation scan output.
- [ ] SCL-045 (P2, TODO) Add fallback-locale behavior test matrix.
- [ ] SCL-046 (P1, TODO) Add route-level locale edge-case tests.
- [ ] SCL-047 (P2, TODO) Document SEO route contracts and locale behavior.
- [ ] SCL-048 (P2, TODO) Add validation for translation key consistency and namespaces.

## Phase 4 - Product Scalability Features
- [ ] SCL-049 (P1, TODO) Add revision diff view (content comparison).
- [ ] SCL-050 (P1, TODO) Add revision restore workflow with safeguards.
- [ ] SCL-051 (P1, TODO) Add publication calendar view for planned content.
- [ ] SCL-052 (P2, TODO) Add scheduler observability for publish command execution.
- [ ] SCL-053 (P2, TODO) Add media deduplication by checksum.
- [ ] SCL-054 (P2, TODO) Add safe replace workflow for duplicate assets.
- [ ] SCL-055 (P1, TODO) Prepare read-only headless API contract for public content.
- [ ] SCL-056 (P2, TODO) Add token-scoped access model for headless read API.
- [ ] SCL-057 (P2, TODO) Add content export endpoint for external consumption.
- [ ] SCL-058 (P2, TODO) Draft multi-tenant readiness architecture baseline.
- [ ] SCL-059 (P2, TODO) Add tenant-aware key strategy for cache/session design.
- [ ] SCL-060 (P3, TODO) Add media lifecycle policy (archive, retention, purge).

## Cross-Cutting Observability and Operations
- [x] SCL-061 (P1, DONE) Add request correlation IDs in logs.
- [ ] SCL-062 (P1, TODO) Integrate backend+frontend error tracking platform.
- [ ] SCL-063 (P1, TODO) Add core operational metrics (queue lag, slow queries, cache hit).
- [ ] SCL-064 (P1, TODO) Expand health checks for DB/cache/queue readiness.
- [ ] SCL-065 (P1, TODO) Define production alerting thresholds and ownership map.

## Implementation Rules
- Keep each task batch to <=10 files.
- Avoid behavior/UI changes unless task explicitly requires it.
- Every task ends with tests/build/smoke validation.
- Update this file status after each task.
