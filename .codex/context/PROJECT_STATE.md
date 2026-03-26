# PROJECT_STATE

Last updated: 2026-03-26

## Product Snapshot
- Name: Featherly CMS
- Goal: deliver a multilingual, block-based CMS with a custom admin panel
- Commercial model: internal/custom deployment model (SaaS assumptions from template are not active constraints)
- Current phase: MVP stabilization and runtime completion

## Confirmed Decisions
- 2026-03-17: translation scanning and integrity checks are part of the baseline workflow.
- 2026-03-24: documentation and agent setup aligned to template structure with Featherly-specific content.

## Technical Baseline
- Backend: Laravel 12 + PHP 8.2+
- Frontend: Vue 3 + Inertia + Pinia + Ziggy
- Mobile: none in repository scope
- Database: relational DB with JSON fields for localized/block content
- Infra: Laravel app runtime + Vite for frontend build/dev

## Current Focus
- Main active objective: transition from feature-expansion execution to scaling backlog lane.
- Top blockers: none for FEX stream closure.
- Success criteria for this phase: pick first scaling backlog slice and continue tiny reversible delivery flow.

## Recent Progress
- 2026-03-25: Completed FEX-001 (admin-only user creation policy guard on `admin.users.store` + feature tests).
- 2026-03-25: Completed FEX-002 (admin user create form defaults + stricter role validation in user create/update flow).
- 2026-03-25: Completed FEX-003 (admin forgot/reset password flow with test coverage).
- 2026-03-25: Completed FEX-004..FEX-008 (installer detect/step1/step2/finalize/lock-hardening flow with feature tests).
- 2026-03-25: Completed FEX-009..FEX-010 (shared DaisyUI validation primitives and rollout to admin auth + installer UI).
- 2026-03-25: Completed FEX-011 (global search backend contract and architecture baseline).
- 2026-03-25: Completed FEX-012..FEX-014 (page/post/project search providers for global admin search backend).
- 2026-03-25: Completed FEX-015..FEX-016 (aggregated search endpoint and ranking policy implementation).
- 2026-03-25: Completed FEX-017..FEX-020 (navbar dropdown search UX with keyboard navigation and async state handling).
- 2026-03-25: Completed FEX-021 (global autosave interval setting key shared to admin frontend).
- 2026-03-25: Completed FEX-022..FEX-023 (autosave timer integration and optimistic-lock conflict guard coverage).
- 2026-03-26: Completed FEX-024..FEX-025 (autosave conflict compare/reload modal without overwrite path + header autosave status indicator; autosave timer paused while conflict is unresolved).
- 2026-03-26: Completed FEX-026..FEX-027 (title editing moved to Info tabs across editor modules; top bar now shows read-only title preview).
- 2026-03-26: Completed FEX-028..FEX-030 (builder keyboard-shortcut service, in-editor shortcuts help modal, and translation seeder keys required by i18n integrity checks).
- 2026-03-26: Completed FEX-031..FEX-033 (media file-type backend filter + media UI filter controls + reusable single-select media picker field with ID storage contract).
- 2026-03-26: Completed FEX-034..FEX-036 (optional multi-select picker flow, ordered selection payload, and multi-preview rendering support in reusable media picker control).
- 2026-03-26: Completed FEX-037..FEX-039 (picker config schema wiring in block settings manager, first block integrations for image/carousel, and regression coverage for filter + media ID persistence).
- 2026-03-26: Completed FEX-040 (documented media picker control contract and linked it from modules architecture doc).
- 2026-03-26: Completed FEX-041..FEX-042 (introduced module block registry contract/service and shared module category loader in base admin content controllers).
- 2026-03-26: Completed FEX-043 (wired module-scoped categories to all block editor pages and validated with module category share feature tests).
- 2026-03-26: Completed FEX-044 (template module block addons are now filtered by template type; slot addons scoped to `page` templates).
- 2026-03-26: Completed FEX-045 (added global composed-block model and migration baseline for reusable user-defined multi-block compositions).
- 2026-03-26: Completed FEX-046 (added composed-block admin CRUD baseline: routes, controller, list/edit views, and management tests).
- 2026-03-26: Completed FEX-047 (added composed block insertion/reference flow in builder via `composed_block` block type and shared composed block library payload).
- 2026-03-26: Completed FEX-048 (safe composed block updates with optimistic lock validation and revision snapshot creation).
- 2026-03-26: Completed FEX-049 (documented contentType/module contract and reference implementation slice).
- 2026-03-26: Completed FEX-050 (added Wave 5 coverage for module scope inheritance/override dedupe and composed block rendering contract persistence/runtime payload).
- 2026-03-26: Completed FEX-051 (added lightweight WYSIWYG editor flow for paragraph block content editing in builder settings).
- 2026-03-26: Completed FEX-052 (removed paragraph alignment control from advanced tab to keep design controls as the primary alignment source).
- 2026-03-26: Completed FEX-053 (extended lightweight WYSIWYG pipeline to heading/text-focused block settings).
- 2026-03-26: Completed FEX-054..FEX-057 (spacing TRBL controls with units + auto mode, breakpoint selector in design panel, and persisted responsive spacing contract with runtime/editor breakpoint resolution).
- 2026-03-26: Completed FEX-058..FEX-060 (dynamic empty-tab filtering in settings sidebar, JS UX logic tests, and architecture docs for builder design controls).
- 2026-03-26: Completed FEX-061..FEX-064 (icon library foundation, Font Awesome registration, searchable icon picker integration in builder icon/stat settings, and shared icon exports for broader admin UI reuse).
- 2026-03-26: Completed FEX-065..FEX-067 (animation preset data model + admin CRUD module, standardized GSAP timeline/tween schema in block settings/runtime, and preset-library linking in builder animation picker).
- 2026-03-26: Completed FEX-068..FEX-070 (brand settings fields, home-page status fallback with coming-soon countdown behavior, and system error-page settings including core 503 custom template fallback rendering).
- 2026-03-26: Completed FEX-071..FEX-076 (shared table multi-select/select-all UX, bulk action API + pages/posts/projects wiring for publish/unpublish/archive/delete, confirmation guard for risky actions, and audit log persistence for bulk operations).
- 2026-03-26: Completed FEX-077..FEX-078 (optimistic bulk table updates with rollback-on-error and integration coverage for bulk authorization/outcomes).
- 2026-03-26: Completed FEX-079..FEX-080 (full regression sweep green after translation-seeder sync and published implementation summary with remaining backlog proposal).

## Working Agreements
- Keep docs, task board, and planning files synchronized.
- Keep changes small and reversible.
- Validate touched areas before marking work done.
- Keep repository artifacts in English.
- Communicate with users in their language.

## Canonical Docs
- `docs/overview.md`
- `docs/product/product.md`
- `docs/architecture/system-architecture.md`
- `docs/planning/mvp-execution-plan.md`
