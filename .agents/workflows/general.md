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

## UI/UX Rules
- Preserve visual consistency across admin modules.
- Reuse builder controls and shared form primitives before adding new UI patterns.
- For animation-related changes, keep progressive enhancement and accessibility defaults.

## Delivery Rules
- Keep changes scoped and reversible.
- Require acceptance evidence before completion.
- Follow the loop: plan -> implement -> test -> architecture review -> sync context -> repeat.
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
- Implement features as vertical slices across UI, logic, API, DB, validation, error handling, and tests.
- Do not mark partial runtime work as done.
- Stop and report when the proper solution is blocked.
