# PROJECT_STATE

Last updated: 2026-05-01

## Product Snapshot
- Name: Featherly CMS
- Goal: deliver a multilingual, block-based CMS with a custom admin panel
- Commercial model: internal/custom deployment model
- Current phase: MVP stabilization and runtime completion

## Confirmed Decisions
- 2026-03-17: translation scanning and integrity checks are part of the baseline workflow.
- 2026-03-24: documentation and agent setup were aligned to the template-era structure with Featherly-specific content.
- 2026-04-16: agent workflow has been refreshed to use stronger `.codex/context` guidance and the plan -> implement -> test -> review loop while preserving Featherly-specific docs as canonical inputs.
- 2026-05-01: project instructions were refreshed with stage-based delivery,
  user-collaboration, world-class delivery, UX quality, security lifecycle,
  deployment, rollback, and observability guidance while preserving
  Featherly-specific Laravel/Vue/i18n constraints.

## Technical Baseline
- Backend: Laravel 12 + PHP 8.2+
- Frontend: Vue 3 + Inertia + Pinia + Ziggy
- Mobile: none in repository scope
- Database: relational DB with JSON fields for localized and block content
- Infra: Laravel runtime + Vite frontend build/dev
- External services: optional Sentry and media/integration surfaces as configured
- MCP / external tools: design-source workflows may use Figma or Stitch where applicable

## Validation Commands
- Backend tests: `php artisan test`
- Static analysis: `composer analyse`
- Frontend lint: `npm run lint`
- Frontend format check: `npm run format:check`
- i18n scan: `php artisan i18n:scan --scope=admin`
- Local dev runtime: `composer run dev`

## Current Focus
- Main active objective: continue the next smallest CMS delivery slice with strong admin, i18n, and builder integrity
- Top blockers:
  - public dynamic routes still need finalization and validation
  - some legacy docs and taxonomy decisions remain open
- Success criteria for this phase:
  - small reversible execution slices
  - synchronized docs and task context
  - no regressions in localization or shared builder/admin patterns

## Working Agreements
- Keep docs, task board, and project state synchronized.
- Keep changes small and reversible.
- Validate touched areas before marking work done.
- Keep repository artifacts in English.
- Communicate with users in their language.
- Use the default loop: plan -> implement -> test -> architecture review -> sync context.
- Use `.codex/templates/task-template.md` with task type, current stage, stage
  deliverable, success signal, validation evidence, and result report.
- Use `.agents/workflows/user-collaboration.md` for ambiguous intent, blocker
  decisions, visual notes, or handoffs.
- Use `.agents/workflows/world-class-delivery.md` for substantial product,
  runtime, release, UX, security, or AI work.

## Canonical Docs
- `docs/overview.md`
- `docs/product/product.md`
- `docs/product/mvp_scope.md`
- `docs/architecture/system-architecture.md`
- `docs/architecture/modules.md`
- `docs/planning/mvp-execution-plan.md`
- `docs/planning/mvp-next-commits.md`
- `docs/planning/open-decisions.md`
- `docs/governance/function-coverage-ledger-standard.md`
- `docs/governance/function-coverage-ledger-template.csv`
- `docs/governance/world-class-product-engineering-standard.md`
- `docs/security/secure-development-lifecycle.md`
- `docs/operations/service-reliability-and-observability.md`
- `docs/operations/post-deploy-smoke.md`
- `docs/operations/rollback-and-recovery.md`
- `docs/ux/design-system-contract.md`
- `docs/ux/experience-quality-bar.md`
- `docs/ux/canonical-visual-implementation-workflow.md`
- `.codex/context/PROJECT_STATE.md`
- `.codex/context/TASK_BOARD.md`

## Autonomous Iteration State
- Current iteration:
- Current operation mode: BUILDER | ARCHITECT | TESTER
- Last completed iteration:
- Last completed task:
- Next required mode: