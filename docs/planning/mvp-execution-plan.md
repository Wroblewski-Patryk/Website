# MVP Execution Plan

## Rules
- Keep tasks tiny and reversible.
- Sequence by dependencies.
- Record progress log after each completed task.
- Validate each task with tests/build/smoke checks.

## Active Program
- Primary roadmap: `docs/planning/scaling-roadmap.md`
- Primary backlog: `docs/planning/scaling-backlog-65.md`
- Active short queue: `docs/planning/mvp-next-commits.md`

## Workstream: Phase 0 - Guardrails and Delivery Safety
- [x] SCL-001 Add PHP static analysis baseline (PHPStan/Psalm) and CI gate
- [x] SCL-002 Add frontend lint/format validation in CI
- [x] SCL-003 Split CI into isolated jobs (tests/build/lint/audit/migrations)
- [x] SCL-004 Add `composer audit` CI gate
- [x] SCL-005 Add migration smoke workflow (`migrate:fresh --seed`)

## Workstream: Phase 1 - Core Stability
- [x] SCL-011 Migrate CRUD validation to dedicated FormRequest classes
- [x] SCL-012 Add DB transactions for multi-step content writes
- [x] SCL-013 Add optimistic locking strategy for concurrent edits
- [x] SCL-014 Standardize API response envelopes for admin endpoints
- [x] SCL-015 Add policy-based authorization for core content models
- [x] SCL-016 Add audit logging for RBAC and settings changes
- [x] SCL-018 Add status value constraints at DB level
- [x] SCL-019 Enforce single default language invariant in DB
- [x] SCL-017 Remove legacy dual-source role ambiguity (`users.role` vs Spatie)
- [x] SCL-021 Harden media upload validation (MIME sniff + file checks)
- [x] SCL-024 Add cache key versioning strategy for global datasets
- [x] SCL-061 Add request correlation IDs in logs
- [x] SCL-028 Index taxonomy-heavy paths (`module`, `type`, `order`)
- [x] SCL-029 Index revisions by morph columns and timestamp

## Workstream: Phase 2 - Performance and Throughput
- [x] SCL-026 Reduce heavy global Inertia shared payloads
- [x] SCL-027 Add query profiling and remove N+1 in public render paths
- [x] SCL-030 Add search strategy for JSON-translated content
- [x] SCL-031 Add pagination strategy for very large media collections
- [x] SCL-032 Introduce cursor pagination where offset scaling hurts
- [x] SCL-033 Define cache TTL matrix (what is forever vs expiring)
- [x] SCL-034 Add route-level response time budget checks
- [x] SCL-035 Build per-module performance smoke script
- [x] SCL-036 Refine Vite chunk strategy to reduce oversized bundles
- [x] SCL-037 Add frontend runtime memory/perf watch for block builder
- [x] SCL-038 Add virtualized rendering for large admin lists/tables

## Workstream: Phase 3 - i18n and SEO Hardening
- [x] SCL-039 Remove locale hardcodes and use active language source everywhere
- [x] SCL-041 Add slug normalization and reserved-path safeguards
- [x] SCL-043 Improve hreflang generation for nested and archive routes
- [x] SCL-044 Add i18n coverage dashboard from translation scan output
- [x] SCL-045 Add fallback-locale behavior test matrix
- [x] SCL-048 Add validation for translation key consistency and namespaces
- [x] SCL-047 Document SEO route contracts and locale behavior
- [x] SCL-042 Improve canonical URL validation/normalization
- [x] SCL-046 Add route-level locale edge-case tests
- [x] SCL-040 Standardize localized slug conflict policy

## Workstream: Phase 4 - Product Scalability Features
- [x] SCL-049 Add revision diff view (content comparison)
- [x] SCL-050 Add revision restore workflow with safeguards
- [x] SCL-051 Add publication calendar view for planned content
- [x] SCL-052 Add scheduler observability for publish command execution
- [x] SCL-053 Add media deduplication by checksum
- [x] SCL-054 Add safe replace workflow for duplicate assets
- [x] SCL-055 Prepare read-only headless API contract for public content
- [x] SCL-056 Add token-scoped access model for headless read API
- [x] SCL-057 Add content export endpoint for external consumption
- [x] SCL-058 Draft multi-tenant readiness architecture baseline
- [x] SCL-059 Add tenant-aware key strategy for cache/session design
- [x] SCL-060 Add media lifecycle policy (archive, retention, purge)

## Progress Log
- 2026-03-24: Introduced scaling execution documentation and 65-item backlog.
- 2026-03-25: Completed SCL-050 revision restore workflow with server-side safeguards and tests.
- 2026-03-25: Completed SCL-051 publication calendar view for planned content across pages, posts, and projects.
- 2026-03-25: Completed SCL-052 scheduler observability for `publish:scheduled` (structured logs + command-level test coverage).
- 2026-03-25: Completed SCL-053 media deduplication metadata (checksum + duplicate reference on upload, no auto-delete behavior).
- 2026-03-25: Completed SCL-054 safe duplicate replace workflow (checksum guard, reference replacement, optional source delete).
- 2026-03-25: Completed SCL-055 headless read API contract draft for public content (`v1` scope and compatibility rules).
- 2026-03-25: Completed SCL-057 content export endpoint (admin protected JSON export contract for pages/posts/projects).
- 2026-03-25: Completed SCL-058 multi-tenant readiness architecture baseline document (isolation principles, rollout, risk controls).
- 2026-03-25: Completed SCL-059 tenant-aware key strategy baseline for cache/session/queue contexts.
- 2026-03-25: Completed SCL-056 token-scoped headless access control (hashed bearer token lookup, scope middleware guard, and feature tests).
- 2026-03-25: Completed SCL-060 media lifecycle policy foundation (archive/retention/purge metadata columns, lifecycle command with dry-run default, and feature tests).
- 2026-03-24: Completed SCL-001/SCL-003/SCL-004 (PHPStan baseline, split CI jobs, security audit gates).
- 2026-03-24: Completed SCL-002/SCL-005 (frontend lint+format CI gate, migration smoke job with sqlite seeding).
- 2026-03-24: Completed SCL-012 (transaction boundaries for create/update flows in page/post/project admin controllers).
- 2026-03-24: Completed SCL-026 (trimmed global Inertia payload for projects and translation query shape).
- 2026-03-24: Completed SCL-006 (bundle-size report generation and CI artifact upload in frontend build job).
- 2026-03-24: Completed SCL-007 (regression-safe PR checklist template and engineering checklist document).
- 2026-03-24: Completed SCL-008 (release candidate checklist and ops runbook rollback hardening).
- 2026-03-24: Completed SCL-009 (task-linked commit and PR naming standard).
- 2026-03-24: Completed SCL-011 (migrated admin page/post/project store/update validation to dedicated FormRequest classes).
- 2026-03-24: Completed SCL-036 (refined Vite vendor chunking; reduced largest non-icon vendor chunk footprint).
- 2026-03-24: Completed SCL-023 (centralized cache invalidation helper for shared Inertia datasets).
- 2026-03-24: Completed SCL-013 (optimistic lock field + stale-write guard for admin page/post/project edits).
- 2026-03-24: Completed SCL-027 (removed public render-path query hotspots via batched template/reference lookups, added opt-in slow-query profiling instrumentation, and reduced shared/archive slug query overhead in Inertia + public page flows).
- 2026-03-24: Completed SCL-014 (standardized shared JSON envelope for explicit app JSON responses, including admin media and permission middleware forbidden response path with compatibility-preserving message field).
- 2026-03-24: Completed SCL-015 (registered content policies for Page/Post/Project and enforced Gate checks in core admin content controllers).
- 2026-03-24: Completed SCL-016 (added persistent audit logs for RBAC role mutations and settings updates, with best-effort logger fallback).
- 2026-03-24: Completed SCL-018 (added DB-level status constraints for publishable tables; checks/triggers cover sqlite and check constraints for other engines).
- 2026-03-24: Completed SCL-019 (added DB-level single-default-language enforcement triggers/functions across sqlite/mysql/pgsql).
- 2026-03-24: Completed SCL-017 (runtime role source now Spatie-only; removed legacy `users.role` sync usage from app flow).
- 2026-03-24: Completed SCL-021 (hardened media upload validation with server-side MIME sniffing and invalid/empty file guards).
- 2026-03-24: Completed SCL-024 (introduced versioned cache key namespace for shared Inertia datasets and translation cache keys).
- 2026-03-24: Completed SCL-061 (added global request correlation ID middleware with log context + `X-Request-Id` response header).
- 2026-03-24: Completed SCL-028 (added composite taxonomy index for module/type/order/id listing paths).
- 2026-03-24: Completed SCL-029 (added composite revisions index for revisionable lookup + chronological access).
- 2026-03-24: Completed SCL-033 (documented shared cache TTL policy matrix and invalidation ownership in planning docs).
- 2026-03-24: Completed SCL-030 (documented DB-aware JSON translated search strategy, adopted reusable locale JSON search helper in admin base listing query, and added localized search regression coverage for page/post/project indexes).
- 2026-03-24: Completed SCL-031 (added bounded media listing pagination controls with validated `per_page` cap and regression tests for JSON listing contract).
- 2026-03-24: Completed SCL-032 (added opt-in cursor pagination mode for media JSON listing with deterministic ordering and regression coverage).
- 2026-03-24: Completed SCL-034 (added opt-in route response budget checks middleware with latency header + warning logs and feature tests).
- 2026-03-24: Completed SCL-035 (added `perf:smoke` command with configurable path set, latency threshold, and regression test coverage).
- 2026-03-24: SCL-039 in progress (replaced multiple hardcoded locale fallbacks with active/default language resolution in public controllers and taxonomy validation locale source).
- 2026-03-24: SCL-039 in progress (replaced frontend locale hardcodes in shared layout/composables/public page + block builder and added shared `default_locale` Inertia prop).
- 2026-03-24: SCL-039 in progress (continued locale hardcode cleanup across admin clients/forms/templates and template reference settings using dynamic default/active locale resolution).
- 2026-03-24: Completed SCL-037 (added opt-in Block Builder runtime perf watcher for slow frames, long tasks, and high heap usage diagnostics).
- 2026-03-25: Completed SCL-038 (added threshold-based virtualized table windowing in shared admin `ResourceTable` with overscan/spacer strategy for large lists).
- 2026-03-25: Completed SCL-042 (added shared canonical URL normalizer, strict `http/https` validation, and request-level canonical payload normalization across admin content forms).
- 2026-03-25: Completed SCL-039 (removed remaining locale hardcoded branching from the public 404 view; locale copy now resolves from current locale context without hardcoded equality checks).
- 2026-03-25: Completed SCL-046 (added route-level locale edge-case coverage for root fallback, locale switch validation, session-locale fallback safety, and dashboard/admin fallback redirects).
- 2026-03-25: Completed SCL-049 (added revision-to-current content diff view in Template editor History tab with block-level added/removed/changed summaries).
- 2026-03-25: Completed SCL-040 (introduced shared `UniqueLocalizedSlug` validation rule and unified slug-conflict handling across page/post/project create+update requests and base admin validation flow).
- 2026-03-25: Completed SCL-041 (added shared localized slug normalization and reserved-route slug protection across page/post/project create+update validation flows, with unit coverage).
- 2026-03-25: Completed SCL-043 (improved hreflang generation for nested and archive-prefixed routes, added `x-default`, and rendered alternate locale tags in shared SEO head component).
- 2026-03-25: Completed SCL-044 (added admin translations coverage dashboard payload and UI cards based on scan-backed translation records, with feature test coverage for Inertia summary contract).
- 2026-03-25: Completed SCL-045 (added fallback-locale routing behavior matrix tests for page, post-archive, and project-archive public routes with sqlite test compatibility shim).
- 2026-03-25: Completed SCL-048 (added strict translation key namespace consistency rule and wired it to translation create validation with dedicated unit coverage).
- 2026-03-25: Completed SCL-047 (documented SEO route contracts, locale resolution precedence, fallback semantics, and hreflang/canonical behavior in architecture docs).
