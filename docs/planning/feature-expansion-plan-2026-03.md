# Feature Expansion Plan (Draft)

Date: 2026-03-25  
Status: EXECUTED (implementation baseline complete through FEX-080)

## Scope Source
- User-provided expansion request covering: admin auth/onboarding, global search, block builder UX, animation module, media filters, icon library packs, settings expansion, list bulk actions, modular content architecture, and block-level editor controls.

## Planning Principles
- Keep tasks tiny and reversible (`<=10` files per task).
- Ship by vertical slices with visible admin value.
- Preserve locale-aware routing and existing content architecture.
- Keep i18n integrity checks in the default delivery flow (`i18n:scan` + translation integrity tests).

## Epic Breakdown

### EPIC A: Admin Access and Setup
- Add admin registration flow.
- Add forgot/reset password flow.
- Add first-run installation flow for server deployment.
- Add DaisyUI-backed validation/error components for consistent forms.

Definition of done:
- New auth/setup paths are permission-safe and tested.
- Installation flow is idempotent and locked after successful setup.
- Validation states are visually consistent across admin forms.

### EPIC B: Global Admin Search (Center Navbar Search)
- Add central navbar search input between logo and action icons.
- Implement unified search endpoint for posts/pages/projects first.
- Add architecture for module-level search providers (settings and others later).
- Add ranking/snippet behavior inspired by modern command-search UX.

Definition of done:
- Search results return mixed entity types with clear labels.
- Keyboard navigation works (`up/down/enter/esc`).
- Search architecture supports additive module providers without core rewrites.

### EPIC C: Block Builder Core UX and Data Model
- Add autosave interval for editor sessions.
- Add media picker field in sidebar with:
  - `mediaType` filter (`all/document/image/audio/video`),
  - optional multi-select mode,
  - built-in preview.
- Move title editing from top bar input to `Info` tab (top bar shows read-only preview).
- Add keyboard shortcuts and in-editor help modal (`info keys` trigger).
- Add GSAP Timeline/Tween support in block-level animation settings.

Definition of done:
- Autosave is conflict-safe and visible in UI.
- Media field contract is reusable by all relevant blocks.
- Keyboard map is discoverable and localized.
- Title source-of-truth is single and consistent.

### EPIC D: Block Extensibility and Module-Aware Blocks
- Add module-scoped block groups/addons shown only in allowed module editors.
- Support composed/user-created blocks (multi-block templates).
- Enable template-specific blocks (`header`, `footer`, `page slot`, `post content slot`) based on template type.
- Implement inheritance strategy across module controllers/models/components for shared contracts.

Definition of done:
- Block registry supports scope rules per module/content type.
- Composed blocks can be created, reused, and versioned.
- Template editor exposes only valid block addons for active template context.

### EPIC E: Animations Module
- Add dedicated admin module for animation presets and timeline/tween assets.
- Integrate module with Block Builder animation settings.

Definition of done:
- Animation assets are CRUD-managed in a dedicated module.
- Blocks can reference module-managed animations safely.

### EPIC F: Media and Icons
- Add media filtering by file type in media library.
- Add icon library module.
- Include Font Awesome Free and up to 5 additional known icon packs (final list pending decision).
- Make icon packs available for admin UI and page builder icon block.

Definition of done:
- Media library filtering is performant and test-covered.
- Icon module exposes searchable icon sets with source metadata/license notes.

### EPIC G: Settings and Runtime Fallback Pages
- Add brand settings: logo (light/dark), favicon, site name, site description.
- Add behavior when configured home page is not public:
  - maintenance/coming soon fallback page,
  - optional publication countdown when status is scheduled.
- Add settings for system pages: `404` and additional error pages.
- Evaluate maintenance mode as optional follow-up.

Definition of done:
- Runtime fallback behavior is deterministic and documented.
- Settings UI can manage brand/system page mappings safely.

### EPIC H: Admin Tables and Bulk Actions
- Add multi-select support in tables.
- Add bulk actions per resource/module.

Definition of done:
- Bulk action permissions are enforced server-side.
- Selections and action results are clear and auditable.

### EPIC I: Content-Type Modularity
- Extend `contentType(module)` architecture (`page/post/template/...`) for scalable module growth.
- Define shared contracts for controllers/models/services.

Definition of done:
- New content-type modules can be added with minimal boilerplate.
- Shared module contract is documented and used by at least one new module slice.

### EPIC J: Block Controls and Responsive Design Settings
- Paragraph block:
  - add simple WYSIWYG for `content`,
  - remove non-useful `alignment` in advanced section.
- Header/text blocks:
  - enable the same simple WYSIWYG editing approach.
- Design settings:
  - spacing controls (`top/right/bottom/left`) with `auto` handling (value input disabled when `auto`),
  - desktop/tablet/phone switch for breakpoint-specific settings.
- Hide empty tabs across settings panels (if no controls inside).

Definition of done:
- Text blocks use a consistent lightweight WYSIWYG.
- Responsive design values are saved and restored per breakpoint.
- Empty tabs are not rendered in UI.

## Proposed Delivery Order (High-Level)
1. EPIC A (auth/setup baseline) + EPIC H (table bulk infra, minimal slice)
2. EPIC B (global search v1: posts/pages/projects)
3. EPIC C (autosave/media input/title move/keyboard basics)
4. EPIC J (WYSIWYG + responsive design controls + hide empty tabs)
5. EPIC F (media file type filter + icon module)
6. EPIC G (brand settings + fallback/coming soon/countdown + system pages)
7. EPIC D + EPIC I (module-aware block architecture and extensibility)
8. EPIC E (animation management module integrated with builder)

## Dependency Notes
- EPIC B depends on a stable cross-module search provider contract.
- EPIC C media picker depends on EPIC F media filtering/query capabilities.
- EPIC D and EPIC I should share one contract design decision to avoid parallel architectures.
- EPIC E depends on GSAP primitives introduced in EPIC C.
- EPIC G runtime fallback behavior depends on routing and publish-state policies.

## Primary Risks
- Scope size is very large; must be split into strict vertical increments.
- Search could become slow without early indexing and provider boundaries.
- Block-builder changes can introduce regressions without snapshot/smoke coverage.
- WYSIWYG integration can inflate payload size if editor choice is too heavy.
- Icon licensing and package footprint may affect build size.

## Required Product Decisions (Before Implementation)
1. Who can self-register in admin (`open`, `invite-only`, `disabled`)?
2. Should installation wizard be web-only, CLI-assisted, or both?
3. Search UX model: instant dropdown, command palette modal, or both?
4. Search v1 entities final list: only `pages/posts/projects` or include `media/templates/settings` immediately?
5. Autosave interval and behavior: fixed (e.g. 2/3/5 min) or user-configurable?
6. Autosave conflict policy: last-write-wins or optimistic lock prompt?
7. Media picker storage model: save full object snapshot or media IDs only?
8. Multi-select media ordering: preserve user-picked order or system sort?
9. Module-scoped blocks: strict allowlist per module or inheritance + override?
10. User-created composed blocks scope: tenant-global, workspace, or per-module?
11. GSAP storage model: JSON timeline schema in DB or code-driven presets?
12. Animation module ownership: global assets only or module-specific libraries?
13. Additional icon packs (max 5): exact packages to include?
14. Fallback page policy when home is non-public: redirect target priority and HTTP status?
15. Countdown behavior for scheduled publish: timezone source and edge-case behavior?
16. Error pages to support first: `404`, `500`, `503`, maintenance?
17. Bulk actions baseline set per module (delete/publish/archive/duplicate/etc.)?
18. WYSIWYG choice: existing stack-compatible editor (exact package)?
19. Breakpoint set finalization: `desktop/tablet/phone` only or custom breakpoints later?
20. Empty-tab hiding rule: static by schema or dynamic by visible controls + permissions?

## Suggested Next Planning Step
- Convert this draft into a 20-item tiny-task queue in `docs/planning/mvp-next-commits.md` after product decisions 1-8 are confirmed.

## Confirmed Decisions (2026-03-25)
1. Admin registration:
   - Public registration: disabled.
   - New admin accounts are created by existing admins from admin panel.
   - Public auth may be introduced later for client-area extensions (e.g. shop/newsletter consent flows).
2. Installation flow:
   - Primary path: web installer (WordPress-like first-run).
   - Fresh install detection should trigger setup wizard (DB + language).
   - Seed-only starter content (no imported business content).
   - CLI installer remains optional future enhancement.
3. Global search v1 scope:
   - Architecture must allow multiple entities.
   - First implementation scope: only entities inheriting shared base content module contract (`pages/posts/projects/...`).
4. Global search UX:
   - Start with centered navbar dropdown search.
   - Command palette is deferred.
5. Autosave interval:
   - User-configurable interval in settings.
6. Media picker storage model:
   - Persist media IDs (not full file snapshots).
7. Multi-select media ordering:
   - Preserve user selection order.
8. Block module scoping:
   - Inheritance + override model.
   - Modules can define their own nested block lists/files.
9. User-created composed blocks:
   - Global scope.
10. Icon libraries:
   - Keep current Phosphor usage.
   - Add Font Awesome Free.
11. Home-page fallback behavior:
   - If home is non-public and has no scheduled publish time: serve 404.
   - If home is scheduled: render Coming Soon page with countdown.
12. Autosave conflict policy:
   - Use optimistic lock conflict modal with compare + reload options.
   - Do not provide overwrite action from conflict modal.

## Pending Decision
- None (as of 2026-03-26).

## Implementation Summary (Published 2026-03-26)
- Delivered Waves 1-8 from `docs/planning/feature-expansion-backlog.md` (FEX-001..FEX-080).
- Core shipped areas:
  - admin-only account creation + admin forgot/reset flow + installer lifecycle,
  - global admin search (provider contract + pages/posts/projects UI/UX),
  - autosave, conflict modal, keyboard shortcuts/help, title workflow refinements,
  - media picker control contract (single/multi, type filtering, ordered IDs, previews),
  - module-scoped block registry + composed block model/CRUD/reuse/versioning,
  - lightweight WYSIWYG + responsive spacing/breakpoints + empty-tab hiding,
  - icon module (Phosphor + Font Awesome Free) with picker/renderer integration,
  - animation preset module + GSAP schema alignment + preset linkage in builder,
  - brand/system fallback settings (home status behavior, coming-soon countdown, 404/500/503),
  - shared table multi-select, bulk actions, optimistic rollback, and audit logs.
- Validation evidence:
  - full `php artisan test` suite green (145 passed),
  - `php artisan perf:smoke` passed (no slow/error routes under configured threshold),
  - targeted JS tests for responsive controls, icon search, and bulk optimistic logic.

## Remaining Backlog Proposal
1. Start a new execution lane from `docs/planning/scaling-backlog-65.md` with Phase 5+ priorities.
2. Add UX polish pass for bulk actions:
   - toast outcomes per action,
   - optional undo window for reversible actions.
3. Extend system error template support to 500 fallback test route coverage without route-order coupling.
4. Run focused performance pass on large vendor chunks (`vendor-icons` warning) and split where practical.
