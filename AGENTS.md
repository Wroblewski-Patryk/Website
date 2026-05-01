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
- `.agents/workflows/user-collaboration.md`
- `.agents/workflows/world-class-delivery.md`
- `DEFINITION_OF_DONE.md`
- `INTEGRATION_CHECKLIST.md`
- `NO_TEMPORARY_SOLUTIONS.md`
- `DEPLOYMENT_GATE.md`
- `AI_TESTING_PROTOCOL.md`

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
- `docs/governance/agent-setup-blueprint.md` (optional)
- `docs/governance/world-class-product-engineering-standard.md`
- `docs/governance/function-coverage-ledger-standard.md`
- `docs/governance/function-coverage-ledger-template.csv`

### Operations

- `docs/operations/coolify-vps-deployment-contract.md`
- `docs/operations/post-deploy-smoke.md`
- `docs/operations/rollback-and-recovery.md`
- `docs/operations/service-reliability-and-observability.md`

### Engineering And Security

- `docs/engineering/local-development.md`
- `docs/engineering/testing.md`
- `docs/security/secure-development-lifecycle.md`

### Templates and Review

- `.codex/templates/task-template.md`
- `.codex/templates/project-state-template.md`
- `.codex/templates/deployment-agent-checklist-template.md`
- `.github/pull_request_template.md`

### Architecture and UX Truth

- `docs/architecture/README.md`
- `docs/architecture/architecture-source-of-truth.md`
- `docs/architecture/system-architecture.md`
- `docs/architecture/tech-stack.md`
- `docs/ux/ux-ui-mcp-collaboration.md`
- `docs/ux/design-system-contract.md`
- `docs/ux/experience-quality-bar.md`
- `docs/ux/design-memory.md`
- `docs/ux/visual-direction-brief.md`
- `docs/ux/ui-scorecard.md`
- `docs/ux/pattern-gallery.md`
- `docs/ux/screen-quality-checklist.md`
- `docs/ux/anti-patterns.md`
- `docs/ux/brand-personality-tokens.md`
- `docs/ux/canonical-visual-implementation-workflow.md`
- `docs/ux/background-and-decorative-asset-strategy.md`
- `docs/ux/evidence-driven-ux-review.md`

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
- Every task must declare `Task Type`, `Current Stage`, and `Deliverable For
  This Stage` using `.codex/templates/task-template.md`.
- Supported stages are `intake`, `analysis`, `planning`, `implementation`,
  `verification`, `release`, and `post-release`.
- Do not skip stages implicitly. Do not mark work complete without verification
  evidence and a result report.
- Run relevant validation before proposing a commit.
- Keep docs and context synchronized with implementation changes.
- When active work is unclear, a release or handoff needs confidence, or the
  queue goes stale, use the function coverage ledger standard to turn CMS
  module confidence gaps into explicit evidence, blocker, fix, or scope-decision
  tasks before inventing new feature work.
- If a coverage ledger exists, derive follow-up tasks in this order: release
  blockers, implementation-review rows, `P0` evidence rows, `P0/P1` unverified
  rows, then lower-priority scope decisions.
- Do not turn every `PARTIAL` or evidence-missing ledger row into feature work.
  Plan verification first, then create a narrow fix only when proof or code
  inspection finds a real defect.
- Follow the default loop:
  - plan
  - implement
  - test
  - review architecture and risk follow-up
  - sync task/state/docs
  - repeat
- Follow `.agents/workflows/user-collaboration.md` when intent, blocker
  decisions, active visual notes, or handoff expectations need to stay explicit.
- Follow `.agents/workflows/world-class-delivery.md` for substantial product,
  runtime, release, UX, security, or AI work.
- For substantial changes, define why the work matters, the smallest safe
  slice, the success signal, the main failure mode, and the rollback or recovery
  path.
- For deployable services or important journeys, use
  `docs/operations/service-reliability-and-observability.md` to define health,
  observability, alerting, and rollback expectations when appropriate.
- For auth, AI, secrets, permissions, integrations, or user-data work, use
  `docs/security/secure-development-lifecycle.md`.

## Stack-Specific Quality Gate

Primary automated checks in this repo are:

- `php artisan test`
- `composer analyse`
- `npm run lint`
- `npm run format:check`

Use narrower checks when useful, for example:

- `php artisan test --filter=SomeFeatureTest`
- `php artisan i18n:scan --scope=admin`

## Autonomous Engineering Loop

Follow `docs/governance/autonomous-engineering-loop.md` for every autonomous iteration:

1. analyze current state
2. select exactly one priority task
3. plan implementation
4. execute implementation
5. verify and test
6. self-review
7. update documentation and knowledge

Before starting an iteration, perform the process self-audit from that document. Do not continue until all seven steps, one-task scope, and the correct operation mode are represented in the task contract.

Operation mode rotates by iteration number:

- `BUILDER`: default mode
- `ARCHITECT`: every third iteration, unless the iteration is also a tester iteration
- `TESTER`: every fifth iteration
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
- Codex AI Red Team Agent: `.codex/agents/ai-red-team-agent.md`

## Trigger Intent

If the user sends a short execution nudge (`start`, `go`, `next`, `run`, `dzialaj`, `lecimy`):

1. Read `.codex/context/TASK_BOARD.md`.
2. Take the first `READY` or `IN_PROGRESS` task.
3. If no task is ready, derive the smallest viable one from `docs/planning/mvp-next-commits.md`, `docs/planning/mvp-execution-plan.md`, `docs/planning/open-decisions.md`, and active function coverage artifacts when present, then record it.
4. Execute exactly one tiny task.
5. Run relevant checks.
6. Update planning, docs, and context files.
7. Return files changed, checks run, and next tiny task.

## Project-Specific Focus

- Featherly is a multilingual, block-based CMS with a custom admin panel.
- Preserve locale-aware routing conventions and route segregation.
- Keep i18n integrity (`php artisan i18n:scan --scope=admin`) in the workflow when copy changes.
- Prefer extending shared admin components, builder controls, and content-module contracts over one-off implementations.

## UX/UI Contract

For UX/UI tasks, always include:

- design source reference when available
- existing design-system pattern reused, or a clear justification for a new
  shared pattern
- expected states: `loading`, `empty`, `error`, `success`
- responsive checks: desktop, tablet, and mobile when relevant
- accessibility checks
- screenshot or parity evidence for substantial visual work

Use the UX starter docs to keep direction explicit:

- `docs/ux/design-system-contract.md`
- `docs/ux/experience-quality-bar.md`
- `docs/ux/visual-direction-brief.md`
- `docs/ux/pattern-gallery.md`
- `docs/ux/screen-quality-checklist.md`
- `docs/ux/anti-patterns.md`
- `docs/ux/canonical-visual-implementation-workflow.md`
- `docs/ux/evidence-driven-ux-review.md`

## Deployment Contract

Featherly currently declares a Laravel runtime with Vite frontend build/dev in
`.codex/context/PROJECT_STATE.md`. If a deployment target is confirmed or
changed, update:

- `.codex/context/PROJECT_STATE.md`
- `docs/operations/coolify-vps-deployment-contract.md` or the documented
  alternative deploy contract
- `docs/operations/post-deploy-smoke.md`
- `docs/operations/rollback-and-recovery.md`
- `docs/operations/service-reliability-and-observability.md`

## Commit Rule

Do not create a commit when required checks for the touched scope are failing, unless the user explicitly accepts the risk.

## Production Hardening Gate

Canonical hardening files:

- `DEFINITION_OF_DONE.md`
- `INTEGRATION_CHECKLIST.md`
- `NO_TEMPORARY_SOLUTIONS.md`
- `DEPLOYMENT_GATE.md`
- `AI_TESTING_PROTOCOL.md`
- `.codex/agents/ai-red-team-agent.md`

Every task must include Goal, Scope, Implementation Plan, Acceptance Criteria, Definition of Done, and Result Report. A task is `DONE` only after `DEFINITION_OF_DONE.md` is satisfied with evidence.

Runtime features must be vertical slices across UI, logic, API, DB, validation, error handling, and tests. Partial implementations, placeholders, mock-only behavior, fake data, temporary fixes, and hidden bypasses are forbidden.

AI systems must be tested against prompt injection, data leakage, and unauthorized access before deployment. AI features require reproducible multi-turn scenarios from `AI_TESTING_PROTOCOL.md` and red-team review when risk is meaningful.
