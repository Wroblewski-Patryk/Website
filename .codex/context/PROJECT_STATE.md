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
- 2026-05-01: automatic updates were defined as an environment-adaptive Update
  Manager contract: update checks are application-owned, update application is
  delegated to safe drivers such as Coolify, Docker, Git, archive, or manual
  workflows, and unsupported hosts must fail closed to notification/manual mode.
- 2026-05-01: `docs/architecture` was synchronized with verified current
  implementation surfaces through `current-implementation-map.md`, including
  routing, admin/content boundaries, headless export, media lifecycle,
  operations, reliability, and known architecture debt.
- 2026-05-01: System Update Manager update-check baseline was verified:
  admin settings expose status/preferences, manual check uses a trusted
  manifest, `updates:check` is scheduled daily, and unsupported automatic
  application remains fail-closed to manual/status mode.
- 2026-05-01: System Update Manager manual/fake apply contract was verified:
  manual apply records operator instructions without mutating files, fake apply
  is config-gated for tests, and high-risk/manual-review releases are blocked.
- 2026-05-01: System Update Manager production-driver preflight baseline was
  verified: Coolify reports configured webhook readiness without exposing
  secrets, archive reports missing or writable paths, and both remain
  preflight-only with runtime code replacement disabled.
- 2026-05-01: System Update Manager Coolify apply trigger path was added behind
  `FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED`; tests verify webhook trigger with
  HTTP fakes, disabled trigger sends no request, and status never exposes the
  webhook secret.
- 2026-05-01: System Update Manager post-deploy confirmation was added through
  `updates:confirm`, which marks a triggered deployment complete only after
  the running `APP_VERSION` matches the expected target release.
- 2026-05-01: System Update Manager confirmation now reuses a shared
  operational health checker, so matching runtime versions are marked
  `confirmed` only when database, cache, and queue readiness checks also pass.
- 2026-05-01: Coolify update rollout runbook was added to define the operator
  evidence path from `updates:check` and webhook trigger through
  `updates:confirm`, post-deploy smoke, and Coolify deployment-history
  rollback.
- 2026-05-01: Archive update driver preflight now fails closed unless the
  release manifest stores `release_archive_url` and a valid 64-character
  `release_archive_sha256`, keeping archive apply preflight-only until
  download, verification, staging, and switch execution are implemented.
- 2026-05-01: Archive update driver can now download a release archive to
  staging and verify SHA-256 without extracting, migrating, switching live
  files, or marking the update applied.
- 2026-05-01: Archive update verification now records ZIP extraction readiness;
  if PHP `ZipArchive` is unavailable, the staged archive remains verified but
  extraction is marked `unavailable` and live files remain untouched.
- 2026-05-01: Docker and Git runtime update drivers were explicitly deferred
  from System Update Manager v1 through DEC-009; Docker/Git deployments remain
  platform/operator-owned until dedicated contracts exist.
- 2026-05-01: Archive update driver now supports no-switch staging extraction
  validation when PHP `ZipArchive` is available; tests verify required release
  files are present and missing-file archives fail closed without touching live
  files.
- 2026-05-01: Archive staging validation now writes a no-switch switch/rollback
  plan with preserve paths, required pre-switch approvals, and rollback
  strategy before any future live file replacement is allowed.
- 2026-05-01: Archive live switch execution was added behind
  `FEATHERLY_UPDATE_ARCHIVE_SWITCH_ENABLED`; tests verify previous release
  backup, `.env`/`storage`/`public/storage` preservation, staged release file
  switch, and status update without enabling automatic archive apply by default.
- 2026-05-01: Archive rollback execution was added through
  `updates:rollback-archive --force`; tests verify the recorded backup path
  restores release files while preserving current `.env` and `storage`.
- 2026-05-01: System Update Manager v1 implementation is locally verified for
  discovery, safe driver gating, Coolify trigger/confirmation contracts, archive
  verify/stage/switch/rollback, and Docker/Git v1 deferral. Coolify
  production-ready rollout evidence remains blocked on an external configured
  Coolify staging/live environment and must satisfy the deployment gate before
  production enablement.
- 2026-05-02: Module contract audit verified pages/posts/projects as aligned
  with the strongest shared content contract and queued forms/templates
  ownership hardening plus `projects.category` retirement as narrow follow-up
  tasks.
- 2026-05-02: Forms and templates were confirmed as settings-owned admin
  modules and hardened with dedicated create/update FormRequests using
  `manage-settings` authorization, plus regression coverage for validation and
  editor denial.
- 2026-05-02: Project category compatibility was settled for V1:
  module-scoped project taxonomies are canonical, while `projects.category`
  remains a read-only fallback only until a later inventory/backfill/removal
  migration plan can retire it safely.
- 2026-05-02: Residual root documentation was normalized: README and product
  overview now reflect current routing/category/update-manager state, the root
  changelog is Featherly-specific, and the bootstrap checklist is explicitly a
  deprecated template artifact.

## Technical Baseline
- Backend: Laravel 12 + PHP 8.2+
- Frontend: Vue 3 + Inertia + Pinia + Ziggy
- Mobile: none in repository scope
- Database: relational DB with JSON fields for localized and block content
- Infra: Laravel runtime + Vite frontend build/dev
- Updates: application-owned update discovery with environment-specific
  application drivers, documented in
  `docs/architecture/system-update-manager-contract.md`; current runtime
  implementation supports update discovery/status/manual instructions and
  config-gated fake apply tests plus a config-gated Coolify webhook trigger
  with CLI version and operational health confirmation; Docker/Git code
  replacement remains unavailable; archive verification can download and verify
  a staged artifact, validate extracted staging structure, and record a switch
  plan; gated archive switch can replace a configured release path with backup
  evidence when explicitly enabled, archive rollback can restore the recorded
  backup path, Docker/Git runtime drivers are deferred from v1, and Coolify
  production readiness is blocked until captured staging/live rollout evidence
  exists for the target environment
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
  - Coolify production update enablement still needs staging/live rollout evidence
  - legacy project category column removal needs a later data inventory and
    backfill plan before the fallback can be removed safely
  - local READY task queue is empty after residual docs normalization; refill
    should come from `mvp-next-commits.md` or the scaling backlog
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
- `docs/architecture/current-implementation-map.md`
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
