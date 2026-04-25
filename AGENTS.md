# AGENTS.md - Featherly CMS

## Purpose

This repository follows a project-specific multi-agent workflow so delivery can
move quickly without losing architecture truth, localization integrity, and
task/context discipline.

## Canonical Files

### Core Context

- `.codex/context/PROJECT_STATE.md`
- `.codex/context/TASK_BOARD.md`
- `.codex/context/LEARNING_JOURNAL.md`
- `.agents/workflows/general.md`
- `.agents/workflows/documentation-governance.md`
- `.agents/workflows/subagent-orchestration.md`

### Planning

- `docs/planning/mvp-execution-plan.md`
- `docs/planning/mvp-next-commits.md`
- `docs/planning/open-decisions.md`

### Governance

- `docs/governance/working-agreements.md`
- `docs/governance/language-policy.md`
- `docs/governance/repository-structure-policy.md`
- `docs/governance/subagent-delegation-policy.md`
- `docs/governance/code-quality-guardrails.md` (optional)
- `docs/governance/template-usage.md`

### Architecture and UX Truth

- `docs/architecture/README.md`
- `docs/architecture/architecture-source-of-truth.md`
- `docs/architecture/system-architecture.md`
- `docs/architecture/tech-stack.md`
- `docs/ux/ux-ui-mcp-collaboration.md`

## Core Rules

### 1. Architecture Is Source Of Truth

- `docs/architecture/` is the architecture authority.
- Implementation must stay aligned with approved architecture docs.
- If implementation does not fit architecture, stop and escalate before coding
  around it.

### 2. Critical Prohibitions

- Do not create new systems without explicit approval.
- Do not introduce workaround-only paths.
- Do not duplicate logic already covered by existing mechanisms.
- Always reuse approved patterns first.

### 3. Decision Mode For Mismatches

When architecture and implementation clash:

1. describe the problem
2. propose 2 to 3 valid options
3. wait for explicit user decision

### 4. Featherly-Specific Constraints

- preserve localized routing split under `/{locale}` and `/{locale}/admin`
- preserve admin/public boundary contracts and authorization rules
- preserve block-builder content contracts and shared admin form primitives
- preserve translation scanning and integrity expectations for i18n-sensitive
  changes

### 5. Delivery Discipline

- Keep changes tiny, reversible, and evidence-backed.
- Run relevant validation before proposing a commit.
- Keep docs and context synchronized with implementation changes.
- Follow the default loop:
  - plan
  - implement
  - test
  - review architecture and risk follow-up
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
