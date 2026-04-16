# AGENTS.md - Featherly CMS

## Purpose

This repository uses a project-specific agent workflow so Codex and related agents can keep Laravel, Vue/Inertia, docs, and localization behavior aligned while the CMS continues to grow.

## Canonical Files

- `docs/overview.md`
- `docs/product/product.md`
- `docs/product/mvp_scope.md`
- `docs/architecture/system-architecture.md`
- `docs/architecture/modules.md`
- `docs/governance/working-agreements.md`
- `docs/planning/mvp-execution-plan.md`
- `docs/planning/mvp-next-commits.md`
- `docs/planning/open-decisions.md`
- `.codex/context/TASK_BOARD.md`
- `.codex/context/PROJECT_STATE.md`
- `.agents/workflows/general.md`
- `.agents/workflows/subagent-orchestration.md`

## Core Rules

- Keep repository artifacts in English.
- Communicate with the user in the user's language.
- Treat docs plus `.codex/context` files as the operating truth for this repo.
- If code and docs diverge, update both in the same task.
- Preserve Featherly's localized routing split and admin/public boundaries.
- Prefer existing feature-first frontend structure under `resources/js/features/admin/`.
- Reuse shared block-builder and admin form patterns before inventing new UI systems.
- Keep changes tiny, reversible, and evidence-backed.
- Run relevant validation before proposing a commit.
- For i18n-sensitive changes, keep translation scanning and integrity expectations in the delivery loop.
- Follow the default loop:
  - plan
  - implement
  - test
  - review whether architecture or task breakdown can be improved
  - sync task/state/docs
  - repeat

## Stack-Specific Quality Gate

Primary automated checks in this repo are:

- `php artisan test`
- `composer analyse`
- `npm run lint`
- `npm run format:check`

Use narrower checks when useful, for example:

- `php artisan test --filter=SomeFeatureTest`
- `php artisan i18n:scan --scope=admin`

## Agent Catalog

- Planner: `.agents/prompts/planner.md` or `.claude/agents/planner.agent.md`
- Product Docs: `.agents/prompts/product-docs.md` or `.claude/agents/product-docs.agent.md`
- Backend Builder: `.agents/prompts/backend-builder.md` or `.claude/agents/backend-builder.agent.md`
- Frontend Builder: `.agents/prompts/frontend-builder.md` or `.claude/agents/frontend-builder.agent.md`
- QA/Test: `.agents/prompts/qa-test.md` or `.claude/agents/qa-test.agent.md`
- Security: `.agents/prompts/security-auditor.md` or `.claude/agents/security-auditor.agent.md`
- DB/Migrations: `.agents/prompts/db-migrations.md` or `.claude/agents/db-migrations.agent.md`
- Ops/Release: `.agents/prompts/ops-release.md` or `.claude/agents/ops-release.agent.md`
- Code Review: `.agents/prompts/code-reviewer.md`
- Codex Documentation Agent: `.codex/agents/documentation-agent.md`
- Codex Planning Agent: `.codex/agents/planning-agent.md`
- Codex Execution Agent: `.codex/agents/execution-agent.md`
- Codex Review Agent: `.codex/agents/review-agent.md`

## Trigger Intent

If the user sends a short execution nudge (`start`, `go`, `next`, `run`, `dzialaj`, `lecimy`):

1. Read `.codex/context/TASK_BOARD.md`.
2. Take the first `READY` or `IN_PROGRESS` task.
3. If no task is ready, derive the smallest viable one from `docs/planning/mvp-next-commits.md`, `docs/planning/mvp-execution-plan.md`, and `docs/planning/open-decisions.md`, then record it.
4. Execute exactly one tiny task.
5. Run relevant checks.
6. Update planning, docs, and context files.
7. Return files changed, checks run, and next tiny task.

## Project-Specific Focus

- Featherly is a multilingual, block-based CMS with a custom admin panel.
- Preserve locale-aware routing conventions and route segregation.
- Keep i18n integrity (`php artisan i18n:scan --scope=admin`) in the workflow when copy changes.
- Prefer extending shared admin components, builder controls, and content-module contracts over one-off implementations.

## Commit Rule

Do not create a commit when required checks for the touched scope are failing, unless the user explicitly accepts the risk.
