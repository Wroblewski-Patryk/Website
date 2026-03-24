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
- [ ] SCL-015 Add policy-based authorization for core content models

## Workstream: Phase 2 - Performance and Throughput
- [x] SCL-026 Reduce heavy global Inertia shared payloads
- [ ] SCL-027 Add query profiling and remove N+1 in public render paths
- [x] SCL-036 Refine Vite chunk strategy to reduce oversized bundles

## Workstream: Phase 3 - i18n and SEO Hardening
- [ ] SCL-039 Remove locale hardcodes and use active language source everywhere
- [ ] SCL-042 Improve canonical URL validation/normalization
- [ ] SCL-046 Add route-level locale edge-case tests

## Workstream: Phase 4 - Product Scalability Features
- [ ] SCL-049 Add revision diff view (content comparison)
- [ ] SCL-051 Add publication calendar view for planned content
- [ ] SCL-055 Prepare read-only headless API contract for public content

## Progress Log
- 2026-03-24: Introduced scaling execution documentation and 65-item backlog.
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
- 2026-03-24: SCL-027 in progress (removed template-reference N+1 in `BlockContentService` via batched template preload; query profiling pass remains).
