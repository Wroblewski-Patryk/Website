---
description: Workspace rules for Featherly
---

# General Workspace Rules

## Stack Snapshot
- Backend: Laravel 12, PHP 8.2+
- Frontend: Vue 3, Inertia.js, Pinia, Ziggy
- Styling/UI: Tailwind CSS, DaisyUI, custom admin components
- Runtime constraints:
  - localized routing under `/{locale}` and `/{locale}/admin`
  - block-based content and settings contracts
  - translation scanning and integrity checks are part of delivery hygiene

## Architecture Rules
- Keep route split explicit: `auth`, `admin`, `public`.
- Preserve block-based content model and module contracts.
- Prefer existing feature-first frontend modules under `resources/js/features/admin/`.
- Keep shared admin UI components centralized before introducing new patterns.
- Keep translation keys and locale-aware behavior explicit.
- Treat `docs/architecture/` as the approved implementation contract.
- If a better design requires changing approved architecture, propose it before
  changing code direction or docs.

## Repository And Docs Rules
- Keep root minimal and intentional.
- Put project documentation under `docs/`.
- Update planning, architecture, operations, or UX docs when behavior or
  structure changes.
- Treat docs parity as a done-state requirement for module, route, and IA
  changes.
- Use `.agents/workflows/documentation-governance.md` to decide where new
  repository truth should live.
- Keep links repository-relative and avoid sibling-repository references.
- Use `.agents/workflows/user-collaboration.md` when user intent, blockers,
  active visual notes, or handoff expectations need to stay explicit.
- Use `.agents/workflows/world-class-delivery.md` for substantial product,
  runtime, release, UX, security, or AI work.
- Use `docs/governance/autonomous-engineering-loop.md` for autonomous iteration structure, process self-audit, one-task selection, and `BUILDER` / `ARCHITECT` / `TESTER` mode rotation.

## UI/UX Rules
- Preserve visual consistency across admin modules.
- Reuse builder controls and shared form primitives before adding new UI patterns.
- For animation-related changes, keep progressive enhancement and accessibility defaults.
- Treat the Featherly visual system as a reuse-first contract.
- If a new visual pattern is necessary, make it reusable and document it in the
  relevant UX docs.
- For UX-heavy work, require states, responsive checks, accessibility checks,
  and parity or screenshot evidence.
- For broad UX review, use `docs/ux/evidence-driven-ux-review.md` and turn
  clickthrough or screenshot evidence into prioritized implementation slices.
- Every important admin screen should make the primary operator question and
  next action clear within seconds.
- Keep feedback local to the action and avoid showing raw backend or provider
  errors directly to end users.
- When a canonical screenshot or mockup exists, require a visual gap audit,
  asset strategy, and screenshot comparison pass.
- Figma is the primary implementation source when available. Stitch is
  draft-only unless Featherly documents an approved exception.

## Deployment Rules

- Featherly's deployment target must be recorded in
  `.codex/context/PROJECT_STATE.md`.
- Keep env ownership, health checks, persistent data, queues/workers, and Vite
  build expectations explicit.
- When runtime behavior changes, review deploy docs, smoke checks, and rollback
  notes in the same task.
- For deployable services or critical journeys, define the relevant health
  check, alert route, and rollback or disable path when appropriate.

## Delivery Rules
- Keep changes scoped and reversible.
- Require acceptance evidence before completion.
- For release readiness, handoffs, incidents, stale queues, or broad confidence
  reviews, use `docs/governance/function-coverage-ledger-standard.md` to map
  Featherly module functions, evidence gaps, blockers, and the next smallest
  verification or fix task.
- If a function coverage ledger exists, update the smallest truthful row after
  verification, fixes, deferrals, or release-gate reruns.
- Follow the loop: plan -> implement -> test -> architecture review -> sync context -> repeat.
- Declare the current delivery stage in each task and keep output aligned to
  that stage only.
- Do not skip from analysis or planning straight to implementation unless
  explicitly requested.
- Run the relevant validation commands from `.codex/context/PROJECT_STATE.md` before every commit.
- Use subagents only according to `.agents/workflows/subagent-orchestration.md`.
- Update context files whenever repo truth changes.

## Production Hardening Delivery Rules

- Use `.codex/templates/task-template.md` for every task and include Goal, Scope, Implementation Plan, Acceptance Criteria, Definition of Done, and Result Report.
- Check `DEFINITION_OF_DONE.md` before any task moves to `DONE`.
- Check `INTEGRATION_CHECKLIST.md` before integrated runtime work is accepted.
- Check `NO_TEMPORARY_SOLUTIONS.md` whenever a blocker appears.
- Check `AI_TESTING_PROTOCOL.md` for AI features.
- Check `DEPLOYMENT_GATE.md` before release or deploy handoff.
- Check `docs/security/secure-development-lifecycle.md` for security,
  permissions, secrets, AI, integrations, or user-data risk.
- Check `docs/operations/service-reliability-and-observability.md` for
  deployable services, public APIs, workers, scheduled jobs, or critical user
  journeys.
- Implement features as vertical slices across UI, logic, API, DB, validation, error handling, and tests.
- Do not mark partial runtime work as done.
- Stop and report when the proper solution is blocked.
