# Feature Expansion Backlog (Execution Queue)

Date: 2026-03-25  
Status: READY_FOR_IMPLEMENTATION

## Usage Rules
- Keep each task tiny and reversible (`<=10` files preferred).
- Complete one task per commit with validation evidence.
- Run i18n integrity flow when user-facing copy changes (`i18n:scan` + translation integrity test).
- Update docs alongside behavioral changes.

## Wave 1: Admin Access + Installer Foundation
- [x] FEX-001 Add admin-only user creation endpoint + policy guard.
- [x] FEX-002 Add admin UI form for creating admin accounts.
- [x] FEX-003 Add email/password reset flow for admin users.
- [x] FEX-004 Add first-run install detection middleware (uninstalled state).
- [x] FEX-005 Add web installer step 1 (database connection validation).
- [x] FEX-006 Add web installer step 2 (default language selection + persistence).
- [x] FEX-007 Add installer finalize step (migrations + seeds + lock flag).
- [x] FEX-008 Add installer lock bypass hardening (cannot re-run after success).
- [x] FEX-009 Add DaisyUI validation/error primitives shared by admin forms.
- [x] FEX-010 Apply shared validation primitives to auth/install forms.

## Wave 2: Global Search v1 (Navbar Dropdown)
- [x] FEX-011 Add backend search provider contract for base-module content types.
- [x] FEX-012 Implement search provider for pages.
- [x] FEX-013 Implement search provider for posts.
- [x] FEX-014 Implement search provider for projects.
- [x] FEX-015 Add aggregated search endpoint with typed result envelope.
- [x] FEX-016 Add query ranking rules (exact title > prefix > partial; status aware).
- [x] FEX-017 Add centered navbar dropdown search UI shell.
- [x] FEX-018 Add keyboard navigation in dropdown (`up/down/enter/esc`).
- [x] FEX-019 Add result routing + badges by entity type.
- [x] FEX-020 Add search request debouncing + loading/empty/error states.

## Wave 3: Block Builder Autosave + UX Baseline
- [x] FEX-021 Add autosave settings key (user-configurable interval).
- [x] FEX-022 Add editor autosave timer service using configured interval.
- [x] FEX-023 Add optimistic lock version check in autosave endpoint.
- [x] FEX-024 Add conflict prompt modal (compare/reload without overwrite).
- [x] FEX-025 Add autosave status indicator in editor header.
- [x] FEX-026 Move title input from top bar to Info tab.
- [x] FEX-027 Replace top bar title input with read-only preview text.
- [x] FEX-028 Add keyboard shortcut map service for block editor.
- [x] FEX-029 Add help modal trigger (`info keys`) with shortcut list.
- [x] FEX-030 Add localization keys for new editor UX copy.

## Wave 4: Media Picker Control + Media Filtering
- [x] FEX-031 Add media library API filter by file type (`all/document/image/audio/video`).
- [x] FEX-032 Add media index UI type filters.
- [x] FEX-033 Add reusable sidebar control: single media picker (ID storage).
- [x] FEX-034 Extend picker to optional multi-select mode.
- [x] FEX-035 Preserve selection order in multi-select payload.
- [x] FEX-036 Add preview rendering for selected media in control.
- [x] FEX-037 Add block-prop schema support for picker config (`type`, `multiple`).
- [x] FEX-038 Add picker integration to first target blocks.
- [x] FEX-039 Add regression tests for filter + picker ID persistence.
- [x] FEX-040 Add docs for media control contract in block builder module docs.

## Wave 5: Block Extensibility + Content-Type Modularity
- [x] FEX-041 Define module block registry contract (inheritance + override).
- [x] FEX-042 Add module-level block list loader from module files.
- [x] FEX-043 Add scope resolver (show only blocks allowed by active module).
- [x] FEX-044 Add template-type-specific block addons (`header/footer/slots`).
- [x] FEX-045 Add global composed-block model + migration.
- [x] FEX-046 Add composed-block CRUD in admin.
- [x] FEX-047 Add composed-block insertion flow in block builder.
- [x] FEX-048 Add versioning for composed blocks (safe updates).
- [x] FEX-049 Add contentType module contract note and reference implementation slice.
- [x] FEX-050 Add tests for scope inheritance, overrides, and composed-block rendering.

## Wave 6: WYSIWYG + Responsive Controls + Empty Tabs
- [x] FEX-051 Integrate lightweight WYSIWYG for paragraph content.
- [x] FEX-052 Remove paragraph advanced alignment control.
- [x] FEX-053 Enable same WYSIWYG pipeline for header/text content blocks.
- [x] FEX-054 Add spacing control group (`top/right/bottom/left`) with unit switch.
- [x] FEX-055 Add `auto` mode behavior disabling numeric value input.
- [x] FEX-056 Add breakpoint switch (`desktop/tablet/phone`) in design card.
- [x] FEX-057 Persist and load spacing settings per breakpoint.
- [x] FEX-058 Hide empty tabs dynamically when no visible controls exist.
- [x] FEX-059 Add UX tests for breakpoint editing and empty-tab behavior.
- [x] FEX-060 Add docs update for design controls and WYSIWYG scope.

## Wave 7: Icons + Animations Module + Runtime Fallback Settings
- [x] FEX-061 Add icon library module foundation.
- [x] FEX-062 Register Font Awesome Free alongside existing Phosphor set.
- [x] FEX-063 Add icon search/select UI for builder icon block.
- [x] FEX-064 Expose icon sets for admin UI usage.
- [x] FEX-065 Add animation module data model + CRUD.
- [x] FEX-066 Add GSAP timeline/tween config schema in block settings.
- [x] FEX-067 Link animation module presets to block animation picker.
- [x] FEX-068 Add brand settings fields (logo light/dark, favicon, site name, site description).
- [x] FEX-069 Add home-page status fallback runtime behavior:
  - non-public unscheduled -> 404
  - scheduled -> Coming Soon + countdown
- [x] FEX-070 Add system page settings for `404` and core error templates.

## Wave 8: Tables Bulk Actions + Stabilization
- [x] FEX-071 Add generic table multi-select state and row checkbox UX.
- [x] FEX-072 Add select-all visible page behavior + clear selection.
- [x] FEX-073 Add bulk action API contract with permission checks.
- [x] FEX-074 Implement bulk publish/unpublish for base content modules.
- [x] FEX-075 Implement bulk archive/delete with safety confirmations.
- [x] FEX-076 Add audit logging for bulk actions.
- [x] FEX-077 Add optimistic updates + rollback for failed bulk operations.
- [x] FEX-078 Add integration tests for bulk authorization and outcomes.
- [x] FEX-079 Run full regression sweep (smoke + performance hot paths).
- [x] FEX-080 Publish implementation summary and remaining backlog proposal.

## Validation Evidence Matrix
- Backend behavior changes: feature tests + policy tests.
- Frontend behavior changes: component/unit tests + targeted smoke.
- Cross-cutting changes (search/builder/tables): manual smoke checklist updates.
- i18n-facing changes: scan output + integrity test output.
