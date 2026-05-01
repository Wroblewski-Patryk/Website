# Current Implementation Map

Date: 2026-05-01  
Status: VERIFIED FROM REPOSITORY STRUCTURE AND CODE READS

## Purpose
This map records the architecture surfaces that currently exist in code so
future work can compare implementation against the canonical architecture
without relying on chat history or stale planning notes.

## Routing Topology
- Technical routes live in `routes/web.php`:
  - `/sitemap.xml`
  - `/robots.txt`
  - `/lang/{lang}`
  - `/headless/content-export`
- Localized auth routes are loaded under `/{locale}` from `routes/auth.php`.
- Localized admin routes are loaded under `/{locale}/admin` from
  `routes/admin.php`.
- Localized public content routes are loaded under `/{locale}` from
  `routes/public.php`.
- Non-prefixed fallbacks in `FallbackController` redirect root, login, admin,
  dashboard, and content paths into localized equivalents.
- Public page, post detail, and project detail resolution share
  `PageController@show`; archive settings own blog and project detail URL
  shape.
- Public taxonomy archive URLs are a V1 posts-only surface. Project taxonomy
  archive exposure is intentionally fail-closed.

## Middleware And Runtime Pipeline
- Web middleware prepends:
  - `RequestCorrelationIdMiddleware`
  - `ResponseBudgetMiddleware`
  - `EnsureApplicationInstalled`
- Web middleware appends `HandleInertiaRequests`.
- Route aliases include locale resolution, permission checks, API token scope
  checks, and Spatie role/permission middleware.
- Laravel exception rendering can serve configured custom `404`, `500`, and
  `503` pages through the public page renderer when settings point to pages.
- Sentry Laravel integration is registered in the exception pipeline when
  configured.

## Admin Boundary
- Admin is an Inertia/Vue application behind localized routes,
  authentication, and permission middleware.
- Admin permissions are grouped around:
  - `view-admin`
  - `manage-content`
  - `manage-settings`
  - `manage-users`
- Admin role users receive a `Gate::before` allow rule.
- Frontend auth props expose the signed-in user, primary role, and flattened
  permission flags.

## Content Write Contract
- Pages, posts, and projects use dedicated FormRequest classes for create and
  update validation.
- Shared admin content behavior is centralized in
  `BaseAdminContentController`.
- Shared persistence for create/update uses
  `AdminContentPersistenceService`.
- Write paths must preserve:
  - authorization through policies/gates
  - canonical URL normalization before validation
  - localized slug validation and reserved-route protection
  - optimistic-lock checks where payloads include `optimistic_lock`
  - database transactions
  - revision snapshots before update/restore where supported
  - taxonomy sync for taxonomy-enabled modules
- Bulk actions for pages, posts, and projects support publish, unpublish,
  archive, and delete with per-model authorization and audit logging.

## Core Data Domains
- Publishable content:
  - `Page`
  - `Post`
  - `Project`
  - `Template`
  - `Form`
- Content support:
  - `Revision`
  - `Taxonomy`
  - `Client`
  - `ComposedBlock`
  - `AnimationPreset`
- Configuration:
  - `Setting`
  - `Language`
  - `Translation`
- Media:
  - `Media`
  - `MediaFolder`
- Security and ops:
  - `User`
  - Spatie roles/permissions
  - `AuditLog`
  - `ApiToken`
  - `FormSubmission`

## Localization And i18n
- Public and admin routes preserve locale prefixes.
- `LocaleMiddleware` resolves route/path/session/default locale and rejects
  inactive locale switch requests.
- Translatable model fields use Spatie Translatable JSON columns.
- The Inertia shared payload exposes active locale, default locale, active
  languages, translation key text, and SEO settings.
- Translation cache keys are locale-scoped and versioned through
  `SharedInertiaCache`.
- Translation scan and integrity checks remain required validation for
  i18n-sensitive changes.

## Public Runtime
- `BlockContentService` resolves nested `template_reference` blocks up to a
  bounded depth and batches template lookup to avoid repeated queries.
- Shared public props include header, footer, theme config, archive slugs,
  composed blocks, animation presets, and taxonomy-backed project payloads.
- Project public payloads go through `ProjectPublicPresenter`, which prefers
  loaded project category taxonomies and falls back to legacy
  `projects.category` only for compatibility.
- SEO metadata is centralized in `SeoService`; canonical and alternate locale
  behavior is documented in `seo-route-contracts.md`.

## Block Builder And Editor
- The admin block builder owns editor state through Pinia
  `useBlockBuilderStore`.
- Module block availability is resolved by `ModuleBlockRegistry` from
  `config/block_builder.php`.
- Module categories are shared to editor pages as `moduleCategories`.
- Current module registry coverage:
  - pages inherit base content
  - posts add `posts_list`
  - projects add `projects_list`
  - templates add template reference and slot blocks
  - forms add form/input blocks
- Editor capabilities include autosave interval sharing, conflict UI,
  keyboard shortcuts, media picker controls, icon picker/renderer,
  responsive spacing, lightweight WYSIWYG text editing, animation settings,
  and dynamic empty-tab filtering.

## Media
- Media records expose a computed public `url`.
- Media supports folders, type filtering, upload validation, picker selection,
  checksum deduplication, duplicate replacement, and lifecycle metadata.
- The default architecture remains public media storage; private signed media
  access is documented as an opt-in future path.
- Media picker block content stores selected media IDs, not opaque frontend
  state.

## Forms
- Form definitions are authored in admin and stored as JSON content/settings.
- Preview route requires authenticated admin access.
- Public submission route is throttled and writes `FormSubmission` records.
- `idempotency_key` allows duplicate-safe submission handling.

## Headless And Integrations
- Implemented read integration is `GET /headless/content-export`.
- It requires a bearer token with `headless:read` or `*` scope.
- Token records store SHA-256 token hashes and fail closed for missing,
  inactive, expired, revoked, or insufficient-scope tokens.
- Export supports page/post/project filtering by type, status, locale, and
  page size.
- The fuller `/api/v1/headless/*` contract remains a target architecture, not
  the currently implemented route set.

## Operations And Reliability
- Scheduler entries:
  - `publish:scheduled` every minute
  - `ops:metrics` every five minutes without overlap
  - `ops:health-check` every five minutes without overlap
  - `updates:check` daily without overlap
- Artisan command surfaces include:
  - translation scan
  - scheduled publish
  - media lifecycle enforcement
  - performance smoke
  - operational metrics
  - operational health check
  - trusted release manifest update check
- Reliability primitives include request correlation IDs, response budget
  logging, optional slow-query profiling, cache key versioning, Sentry hooks,
  shared operational health checks, and ops documentation for smoke, rollback,
  and observability.

## System Update Manager
- Current implemented slice:
  - admin settings expose update preferences and update status
  - manual admin "check now" action posts to `admin.settings.check-updates`
  - manual admin "apply update" action posts to `admin.settings.apply-update`
  - `updates:check` fetches the configured release manifest
  - `updates:apply` runs the configured driver apply contract
  - `updates:confirm` compares the running `APP_VERSION` with the last target
    release version, runs operational health checks, and records post-deploy
    confirmation status
  - scheduler runs `updates:check` daily without overlap
  - status is stored in `system_update_status`
  - failed or invalid manifest checks write failed status and do not apply code
- Current driver behavior:
  - production-safe fallback driver resolves to `manual`
  - manual driver records operator instructions and does not change files
  - fake driver exists only when explicitly enabled through configuration for
    automated tests
  - Coolify driver preflight verifies that a deploy webhook is configured
    without exposing the webhook URL to the browser
  - Coolify apply trigger is implemented behind
    `FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED`; when disabled it records
    `preflight_only` and sends no request
  - Coolify-triggered deployments stay open until a post-deploy
    `updates:confirm` run observes the expected runtime version and healthy
    database/cache/queue readiness
  - archive driver preflight verifies staging/release path configuration and
    writable parent directories, and now fails closed unless release archive
    URL plus SHA-256 metadata are present
  - archive driver apply can download the configured archive to staging and
    verify SHA-256, but does not extract or switch live files
  - archive verification records extraction readiness as `pending` or
    `unavailable` depending on PHP `ZipArchive` support
  - when `ZipArchive` is available, archive driver extracts the verified
    archive into staging and validates required release files without switching
    live files
  - archive staging validation writes a switch plan that records preserve
    paths, required pre-switch approvals, and rollback strategy
  - archive switch execution is available only behind
    `FEATHERLY_UPDATE_ARCHIVE_SWITCH_ENABLED`; it preserves `.env`, `storage`,
    and `public/storage`, backs up the previous release path, and records
    switch evidence
  - archive rollback execution is available through
    `updates:rollback-archive --force` and restores the release path from the
    recorded backup while preserving local runtime paths
  - archive apply execution remains disabled by default and must be explicitly
    enabled by the operator before live files are switched
- Safety posture:
  - no deploy secrets are exposed to the browser
  - admin routes stay behind `manage-settings`
  - update checking is server-side only
  - manual mode fails closed to notification/status/instructions only

## Verification Surface
- Backend feature/unit tests cover public routing, locale fallback, admin CRUD,
  admin search, bulk actions, media, revisions, installer, headless token scope,
  operational commands, system update checks, SEO, slug rules, and translation
  integrity.
- JavaScript tests cover block builder UX utilities, icon library behavior, and
  resource table optimistic bulk behavior.
- Primary commands remain:
  - `php artisan test`
  - `composer analyse`
  - `npm run lint`
  - `npm run format:check`
  - `php artisan i18n:scan --scope=admin`

## Known Architecture Debt
- `projects.category` remains a compatibility fallback. Canonical semantic
  grouping for projects is module-scoped taxonomy.
- The documented full `/api/v1/headless/*` API is not fully implemented; the
  implemented integration route is `/headless/content-export`.
- Some module-level docs may lag behind this map and should be reconciled
  during FEA-011 module contract audit.
- System Update Manager real apply drivers are not fully production-ready yet.
  Coolify has a gated webhook trigger path plus version and operational health
  confirmation plus an operator rollout runbook, but still needs captured
  live-environment rollout evidence before production enablement; archive apply
  has no-switch download/verification, staging extraction validation,
  switch-plan evidence, gated switch execution, and rollback execution; Docker
  and Git runtime drivers are deferred from v1 by DEC-009.
