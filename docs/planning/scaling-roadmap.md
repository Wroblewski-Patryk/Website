# Scaling Roadmap (No Regression Program)

## Goal
Deliver architecture, quality, security, and scalability improvements without changing user-facing functionality or visual behavior unless explicitly approved.

## Non-Negotiable Constraints
- No functional regressions in admin/public runtime.
- No visual regressions in existing UI paths.
- Small reversible batches (`<=10 files` per task).
- One logical task per commit.
- Green validation before and after each task:
  - `php artisan test`
  - `npm run build`
  - targeted smoke checks for touched module.

## Execution Model
1. Pick one backlog task from current `NOW` queue.
2. Implement in a small batch.
3. Run validation gates.
4. Commit with task ID in message.
5. Update progress in planning docs.
6. Move next task from `NEXT` to `NOW`.

## Program Phases

### Phase 0 - Guardrails and Delivery Safety (Week 1)
Outcome: safer and faster iteration pipeline.
- Tooling quality gates in CI (lint/static analysis/audit/migration checks).
- Unified PR/release checklist.
- Baseline bundle and runtime metrics collection.

### Phase 1 - Core Stability (Weeks 2-4)
Outcome: stronger domain consistency and backend safety.
- Move request validation to dedicated request classes.
- Transaction boundaries for multi-step writes.
- DB integrity hardening (constraints, invariants).
- Cache invalidation normalization.

### Phase 2 - Performance and Throughput (Month 2)
Outcome: better scalability under content and traffic growth.
- Remove heavy global payload patterns.
- Query/perf profiling and N+1 removal.
- Pagination and indexing tuning for large datasets.
- Bundle strategy and perf budgets.

### Phase 3 - i18n and SEO Hardening (Month 3)
Outcome: predictable multilingual and SEO behavior at scale.
- Remove locale hardcodes.
- Canonical/alternate URL consistency.
- Translation coverage observability.
- Route/locale edge-case tests.

### Phase 4 - Product Scalability Features (Month 4)
Outcome: operational leverage and future-ready architecture.
- Revision diff/restore.
- Publication calendar workflow.
- Media deduplication.
- Headless read API prep.
- Multi-tenant readiness design baseline.

## Delivery Gates Per Phase
A phase is complete only when:
- All phase tasks are marked DONE in backlog.
- Test/build gates are green.
- No open P0/P1 regression issues.
- Docs for changed areas are updated.
- Rollback strategy is documented.

## Risk Controls
- Feature flags for potentially risky runtime changes.
- Explicit migration rollback notes.
- Staged rollout for infra-sensitive changes.
- "Stop-the-line" rule on regression detection.

## Progress Tracking
- Backlog source: `docs/planning/scaling-backlog-65.md`
- Active queue: `docs/planning/mvp-next-commits.md`
- Main execution log: `docs/planning/mvp-execution-plan.md`

## How to Read Task Priority
- P0: critical risk reduction, blocks safe scaling.
- P1: high value, should be completed in current phase.
- P2: important but can be sequenced after P0/P1.
- P3: optional optimization/documentation enhancement.

## Suggested Commit Format
- `SCL-0XX: short action summary`

Example:
- `SCL-012: add transaction boundaries for content update flow`
