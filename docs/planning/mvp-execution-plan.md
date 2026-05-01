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
- 2026-05-01: Completed FEA-015L by adding no-switch archive extraction
  staging validation for runtimes with PHP `ZipArchive`: verified archives are
  extracted into staging, required Laravel release files are checked, missing
  required files fail closed and remove extracted output, and no live files are
  switched or marked applied.
- 2026-05-01: Completed FEA-015K by closing DEC-009: Docker and Git runtime
  update drivers are deferred from System Update Manager v1, Docker
  deployments should use Coolify/platform rollout paths, Git deployments remain
  operator/manual until a dedicated contract exists, and no placeholder
  Docker/Git driver classes should be added in v1.
- 2026-05-01: Completed FEA-015J by adding archive extraction runtime
  capability evidence: verified archives now record extraction status as
  `pending` when ZIP support exists or `unavailable` when PHP `ZipArchive` is
  missing, keeping live files untouched and making the runtime prerequisite
  explicit before staging extraction work.
- 2026-05-01: Completed FEA-015I by adding no-switch archive download
  verification: archive apply downloads the trusted release archive to staging,
  validates SHA-256, records verification evidence, removes checksum-mismatched
  downloads, and still does not extract, migrate, switch live files, or mark the
  update applied.
- 2026-05-01: Completed FEA-015H by persisting archive release metadata from
  the trusted manifest, requiring `release_archive_url` plus a valid
  64-character `release_archive_sha256` in archive driver preflight, and adding
  regression coverage that archive mode fails closed without integrity metadata
  while still staying preflight-only when metadata is present.
- 2026-05-01: Completed FEA-015G by adding a Coolify update rollout runbook
  that defines preconditions, trigger steps, post-deploy `updates:confirm`,
  evidence capture, health-failure handling, and rollback through Coolify
  deployment history before the driver can be called production-ready.
- 2026-05-01: Completed FEA-015F by extracting shared DB/cache/queue readiness
  probes into `OperationalHealthChecker`, reusing that checker from
  `ops:health-check`, and gating `updates:confirm` so matching runtime
  versions are marked `confirmed` only when operational health passes; failed
  readiness records `confirmation_health_failed` instead.
- 2026-05-01: Completed FEA-015E by adding `updates:confirm`, which compares
  the running `APP_VERSION` with the last target release after a triggered
  deployment, records `confirmed` only when the runtime version matches, keeps
  pending deployments in `awaiting_confirmation`, and documents the smoke path
  for Coolify-triggered updates.
- 2026-05-01: Completed FEA-015D by adding a gated Coolify apply trigger path
  behind `FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED`, posting the configured
  webhook server-side only when enabled, recording `deployment_triggered`
  status without marking the app version current, and covering enabled/disabled
  webhook behavior with HTTP fake regression tests that protect the secret URL
  from status payloads.
- 2026-05-01: Completed FEA-015C by adding production-driver preflight/status
  for Coolify and archive update drivers, exposing driver preflight cards in
  admin settings, protecting Coolify webhook secrets from status payloads, and
  keeping both production drivers preflight-only until apply execution,
  rollback, and integrity checks are implemented.
- 2026-05-01: Completed FEA-015B by adding the System Update Manager driver
  contract, safe manual apply instructions, config-gated fake apply coverage,
  guarded `updates:apply`, admin apply action, and regression tests for fake
  apply success, high-risk block behavior, and manual admin instructions.
- 2026-05-01: Completed FEA-015A by verifying the System Update Manager
  update-check baseline: admin settings status/preferences, manual check
  action, trusted manifest parsing, fail-closed invalid manifest handling,
  daily scheduled `updates:check`, and documentation updates that keep
  automatic apply drivers explicitly out of the current runtime slice.
- 2026-05-01: Continued FEA-015 with an authenticated admin "check now"
  action that forces a server-side manifest refresh even when scheduled checks
  are disabled, exposed release-notes visibility in settings, and added
  regression coverage for the manual trigger plus disabled-scheduler behavior.
- 2026-05-01: Started FEA-015 runtime work by adding the first safe System
  Update Manager slice: persisted update preferences in existing settings,
  added daily `updates:check` manifest polling, exposed admin update status in
  manual-only mode, and covered the slice with targeted settings/command tests.
- 2026-05-01: Completed FEA-016 by removing legacy free-text project category
  authoring from the admin project editor, switching the admin project index to
  taxonomy-backed category labels, and adding regression coverage that hides the
  deprecated `project.category` edit prop while proving taxonomy-first admin
  listing output.
- 2026-05-01: Completed FEA-014 by routing public project detail, project
  archive, and shared runtime `all_projects` payloads through a taxonomy-backed
  presenter with legacy-string fallback, plus regression coverage for
  taxonomy-backed project categories on localized public routes.
- 2026-05-01: Completed FEA-013 by restricting public taxonomy archive
  resolution to `taxonomies.module = posts`, adding regression coverage for
  post taxonomy success and project taxonomy rejection, and syncing the active
  V1 queue to FEA-014.
- 2026-05-01: Completed FEA-010 by auditing taxonomy/category behavior,
  closing DEC-002, declaring module-scoped taxonomies canonical for
  posts/projects, limiting V1 public taxonomy archives to posts in docs, and
  queuing FEA-013/FEA-014 follow-up implementation slices.
- 2026-05-01: Completed FEA-001 by making `routes/public.php` expose explicit
  localized home + named public resolver entrypoints and adding
  `PublicRouteContractTest` coverage for home/page/post/project runtime paths.
- 2026-03-26: Completed FEX-078 by adding integration coverage for bulk action authorization + outcome contract (`BulkActionContractTest` with success/validation/permission cases).
- 2026-03-26: Completed FEX-077 by adding optimistic bulk updates in shared `ResourceTable` with snapshot rollback on request failure.
- 2026-03-26: Completed FEX-076 by adding audit logging for content bulk actions (`content.bulk_action` entries with action/module/ids metadata).
- 2026-03-26: Completed FEX-075 by adding bulk archive/delete behavior for base content modules with explicit confirmation prompt before risky operations.
- 2026-03-26: Completed FEX-074 by implementing bulk publish/unpublish actions for pages/posts/projects and wiring them to shared table bulk controls.
- 2026-03-26: Completed FEX-073 by introducing bulk action API contract endpoints with validation and per-resource authorization checks.
- 2026-03-26: Completed FEX-072 by adding select-all-visible and clear-selection behavior to shared admin table component.
- 2026-03-26: Completed FEX-071 by introducing generic table multi-select row checkbox UX in reusable `ResourceTable`.
- 2026-03-26: Completed FEX-079 by running full regression validation (`php artisan test` all green + `perf:smoke` no slow/error routes).
- 2026-03-26: Completed FEX-080 by publishing implementation summary and next-lane backlog proposal in `feature-expansion-plan-2026-03.md`.
- 2026-03-26: Completed FEX-070 by extending system page settings with additional core error mappings (`500`/`503`) and wiring exception-time custom page rendering fallback for configured statuses.
- 2026-03-26: Completed FEX-069 by enforcing home-page status fallback rules (non-public unscheduled -> 404, planned with future publish date -> Coming Soon with countdown).
- 2026-03-26: Completed FEX-068 by adding brand settings fields (`brand_logo_light`, `brand_logo_dark`, `brand_favicon`) and exposing site-name/description editing alongside settings-media picker flow updates.
- 2026-03-26: Completed FEX-067 by linking animation module presets from shared Inertia library to block animation settings (preset select + apply flow).
- 2026-03-26: Completed FEX-066 by standardizing GSAP block animation schema (enabled/trigger/preset/duration/delay/ease/timeline/tween) with legacy compatibility and timeline filter normalization.
- 2026-03-26: Completed FEX-065 by adding animation preset module data model + admin CRUD (migration/model/controller/routes/Inertia pages + feature coverage).
- 2026-03-26: Completed FEX-064 by exposing centralized icon set exports (`features/admin/icons/index.js`) for reuse across admin UI surfaces.
- 2026-03-26: Completed FEX-063 by adding icon search/select picker UI to builder icon workflows (`icon` block + stat display settings) with shared renderer usage.
- 2026-03-26: Completed FEX-062 by registering Font Awesome Free in frontend bootstrap and supporting prefixed FA rendering in icon runtime component.
- 2026-03-26: Completed FEX-061 by adding icon library module foundation (dataset contract + set metadata + query helper) for admin/builder usage.
- 2026-03-26: Completed FEX-060 by documenting builder design controls and WYSIWYG scope contract in architecture docs.
- 2026-03-26: Completed FEX-059 by adding JS UX logic tests for responsive breakpoint spacing behavior and empty-tab capability filtering.
- 2026-03-26: Completed FEX-058 by hiding block settings tabs dynamically when no controls are available for active block + mode.
- 2026-03-26: Completed FEX-057 by persisting/loading spacing values per breakpoint (`responsiveSpacing.desktop/tablet/phone`) with runtime/editor fallback resolution.
- 2026-03-26: Completed FEX-056 by adding breakpoint switch controls (`desktop/tablet/phone`) inside style spacing panel.
- 2026-03-26: Completed FEX-055 by supporting `auto` unit mode in spacing controls and disabling numeric input in auto mode.
- 2026-03-26: Completed FEX-054 by implementing spacing control group (top/right/bottom/left) with unit switching for margin and padding.
- 2026-03-26: Completed FEX-053 by extending the same lightweight WYSIWYG content pipeline to heading/text-oriented block settings.
- 2026-03-26: Completed FEX-052 by removing paragraph advanced alignment control (design-level alignment remains source of truth).
- 2026-03-26: Completed FEX-051 by integrating a lightweight WYSIWYG editor for paragraph content settings (toolbar + HTML contenteditable flow).
- 2026-03-26: Completed FEX-050 by adding coverage for module inheritance/override dedupe and composed-block rendering contract persistence/runtime payload checks.
- 2026-03-26: Completed FEX-049 by documenting the contentType/module contract and reference implementation slice in architecture docs.
- 2026-03-26: Completed FEX-048 by adding composed block revision snapshots + optimistic-lock conflict protection on updates (safe update path).
- 2026-03-26: Completed FEX-047 by adding `composed_block` insertion flow in block builder (palette entry, settings selector by ID, runtime/editor rendering from shared composed blocks library).
- 2026-03-26: Completed FEX-046 by adding admin CRUD baseline for composed blocks (controller, routes, nav entry, Inertia pages, and feature/model coverage).
- 2026-03-26: Completed FEX-045 by adding global `composed_blocks` data model + migration (`title`, `slug`, `content`, `settings`, `is_active`) with persistence coverage.
- 2026-03-26: Completed FEX-044 by adding template-type-scoped addon visibility for template module blocks (slots visible for `page` templates only).
- 2026-03-26: Completed FEX-043 by wiring module-scoped palette categories into all admin block-editor pages (`pages/posts/projects/templates/forms`) through shared `moduleCategories` props.
- 2026-03-26: Completed FEX-042 by adding `ModuleBlockRegistry` service-backed loader from `config/block_builder.php` and exposing resolved categories via base admin content shared props.
- 2026-03-26: Completed FEX-041 by defining module block registry contract (inheritance/override + per-category block-type dedupe) with unit coverage.
- 2026-03-26: Completed FEX-040 by documenting media picker control schema, payload, and integration contract in architecture docs.
- 2026-03-26: Completed FEX-039 by adding regression checks for media filter behavior and persisted media ID ordering in page block content.
- 2026-03-26: Completed FEX-038 by integrating media picker controls into first target block settings (`image`, `carousel`).
- 2026-03-26: Completed FEX-037 by adding block-level picker configuration contract (`type`, `multiple`) in `BlockSettingsManager`.
- 2026-03-26: Completed FEX-036 by adding preview rendering for selected media in reusable picker control (single and multi-preview variants).
- 2026-03-26: Completed FEX-035 by preserving user click order in multi-select picker payload.
- 2026-03-26: Completed FEX-034 by extending media picker modal/store contract with optional multi-select confirm flow.
- 2026-03-26: Completed FEX-033 by introducing reusable `MediaPickerField` control (single-select, ID storage) and integrating it into block `image` advanced settings.
- 2026-03-26: Completed FEX-032 by adding media type filter controls in admin media toolbar and media picker toolbar.
- 2026-03-26: Completed FEX-031 by adding media index backend filtering by file type (`all/document/image/audio/video`) with feature test coverage.
- 2026-03-26: Completed FEX-027 by replacing editable top-bar title input with read-only title preview in Block Builder header.
- 2026-03-26: Completed FEX-026 by adding title inputs in Info tabs for page/post/project/template/form editors.
- 2026-03-26: Completed FEX-030 by adding missing admin translation seeder keys for new builder/auth/search copy and validating with `TranslationIntegrityTest`.
- 2026-03-26: Completed FEX-029 by adding keyboard-shortcuts help modal and top-bar trigger button in Block Builder.
- 2026-03-26: Completed FEX-028 by introducing reusable `useBuilderShortcuts` map/handler service and wiring editor actions.
- 2026-03-26: Completed FEX-025 by adding autosave status badge in Block Builder header (saved/unsaved/saving/conflict states).
- 2026-03-26: Completed FEX-024 by adding autosave conflict modal with compare summary and reload option, explicitly without overwrite action.
- 2026-03-25: Completed FEX-023 by validating stale optimistic-lock rejection on autosave-like update payloads (feature coverage for page update lock conflict).
- 2026-03-25: Completed FEX-022 by adding interval-driven autosave timer service in Block Builder and wiring autosave hooks to page/post/project/template/form editors.
- 2026-03-25: Completed FEX-021 by introducing shared `builder_autosave_interval_minutes` setting key and exposing normalized `builder_settings.autosave_interval_minutes` in Inertia shared props.
- 2026-03-25: Completed FEX-020 by adding debounce flow and loading/empty/error states in global admin search dropdown.
- 2026-03-25: Completed FEX-019 by wiring search result navigation and entity-type badges in navbar dropdown results.
- 2026-03-25: Completed FEX-018 by adding keyboard navigation (`up/down/enter/esc`) for global admin search dropdown.
- 2026-03-25: Completed FEX-017 by adding centered navbar search UI shell in admin layout.
- 2026-03-25: Completed FEX-016 by introducing backend ranking strategy for search providers (exact > prefix > partial with status-aware boost).
- 2026-03-25: Completed FEX-015 by adding aggregated admin search endpoint (`admin.search.index`) with standardized typed JSON envelope.
- 2026-03-25: Completed FEX-014 by implementing `ProjectSearchProvider` for normalized project search results in admin search contract.
- 2026-03-25: Completed FEX-013 by implementing `PostSearchProvider` for normalized post search results in admin search contract.
- 2026-03-25: Completed FEX-012 by implementing `PageSearchProvider` for normalized page search results in admin search contract.
- 2026-03-25: Completed FEX-011 by adding backend admin-search provider contract (`AdminSearchProvider`, `AdminSearchResult`, `AdminSearchManager`) with unit coverage and architecture note.
- 2026-03-25: Completed FEX-010 by applying shared validation primitives to admin auth flows and installer alerts (login/forgot/reset + install steps).
- 2026-03-25: Completed FEX-009 by introducing reusable DaisyUI validation/status primitives (`FormFieldError`, `FormStatusAlert`) for admin forms.
- 2026-03-25: Completed FEX-008 by hardening installer access guard: `install.*` routes are blocked after installation state is detected.
- 2026-03-25: Completed FEX-007 by adding installer finalize action (env persistence, migrate + seed execution, default language application, and installer lock creation).
- 2026-03-25: Completed FEX-006 by adding installer step 2 for default language selection with session persistence and validation sequencing.
- 2026-03-25: Completed FEX-005 by adding installer step 1 database connection validation (SQLite/MySQL/PostgreSQL probe + session persistence).
- 2026-03-25: Completed FEX-004 by introducing global install-state middleware and installer entry route redirect flow for uninitialized environments.
- 2026-03-25: Completed FEX-003 by adding admin forgot/reset password flow (routes, controller actions, Inertia views) and feature tests for reset-link dispatch and token-based password reset.
- 2026-03-25: Completed FEX-002 by finalizing admin user creation form defaults (preselected `admin` role), enforcing role validation on create/update, and adding feature coverage for create-form authorization and defaults.
- 2026-03-25: Completed FEX-001 by adding `UserPolicy`-based admin-only guard for user creation endpoint (`admin.users.store`) with feature tests covering admin allow + non-admin deny paths.
- 2026-03-25: Planned feature expansion stream (FEX-001..FEX-080) in `docs/planning/feature-expansion-backlog.md` with confirmed product decisions for admin auth, installer mode, search v1 scope, block modularity, icon scope, and home fallback behavior.
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
- 2026-03-25: Completed SCL-062 error tracking integration (Sentry backend exception pipeline + opt-in frontend Vue SDK wiring via env DSN settings).
- 2026-03-25: Completed SCL-063 operational metrics snapshot (queue lag, slow-query event count, and cache hit probe command + scheduler hook).
- 2026-03-25: Completed SCL-064 operational readiness checks (db/cache/queue health command with structured JSON output and scheduler hook).
- 2026-03-25: Completed SCL-065 production alerting baseline (threshold matrix, ownership map, escalation timers, and ops doc linkage).
- 2026-03-25: Completed SCL-010 architecture boundary checks (Deptrac config, composer script, CI job for layer dependency enforcement).
- 2026-03-25: Completed SCL-020 idempotent form submission handling (hashed idempotency key support, DB uniqueness guard, and duplicate-safe success response path).
- 2026-03-25: Completed SCL-022 private media signed-access evaluation (opt-in rollout strategy, risk matrix, and architecture references without default runtime change).
- 2026-03-25: Completed SCL-025 domain service extraction for repetitive admin content persistence workflows (create/update transaction, revision, taxonomy sync).
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
