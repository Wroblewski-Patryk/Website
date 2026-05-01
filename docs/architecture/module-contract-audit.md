# Module Contract Audit

Date: 2026-05-02
Status: VERIFIED FROM CODE READS AND TARGETED TESTS

## Purpose

This audit compares the current implementation contract for pages, posts,
projects, forms, and templates against the architecture map. It is intended to
turn module drift into explicit follow-up tasks instead of broad refactors.

## Shared Content Contract

Pages, posts, and projects currently follow the strongest shared content
contract:

- localized admin routes under `/{locale}/admin`
- admin resource routes protected by `permission:manage-content`
- `BaseAdminContentController` for listing, shared props, taxonomy sync,
  revision restore, optimistic-lock checks, and bulk actions
- dedicated create/update FormRequest classes
- `AdminContentPersistenceService` transactions for create/update and relation
  sync
- `ContentPolicy` authorization checks inside controllers
- localized slug normalization, reserved-route validation, canonical URL
  normalization, SEO fields, publish status handling, revisions, and block
  builder module categories

Forms and templates reuse meaningful parts of this contract. FEA-017 confirms
they are settings-owned modules and hardens them with dedicated FormRequests
that authorize through `manage-settings`.

## Module Matrix

| Module | Admin Route Owner | Validation | Authz | Taxonomy | Revision | Public Runtime |
| --- | --- | --- | --- | --- | --- | --- |
| Pages | `manage-content` | Dedicated FormRequests | `ContentPolicy` gates | No | Yes | Home and dynamic page resolver |
| Posts | `manage-content` | Dedicated FormRequests | `ContentPolicy` gates | Posts categories/tags | Yes | Blog archive/detail via configured archive page |
| Projects | `manage-content` | Dedicated FormRequests | `ContentPolicy` gates | Projects categories/tags | Yes | Project archive/detail via configured archive page |
| Forms | `manage-settings` | Dedicated FormRequests | Route middleware + FormRequest authorization | No | No explicit revision route | Preview + throttled public submit |
| Templates | `manage-settings` | Dedicated FormRequests | Route middleware + FormRequest authorization + system delete guard | No | Yes | Header/footer/sidebar/page layout resolution |

## Findings

### Pages

- Contract fit: strong.
- Uses `Page`, admin `PageController`, page FormRequests, `ContentPolicy`,
  shared persistence, revisions, bulk actions, template overrides, and public
  dynamic resolution.
- No follow-up required from this audit.

### Posts

- Contract fit: strong.
- Uses post FormRequests, taxonomies, shared persistence, revisions, bulk
  actions, post detail resolution through the configured blog archive page, and
  posts-only public taxonomy archives.
- No follow-up required from this audit.

### Projects

- Contract fit: mostly strong.
- Uses project FormRequests, client relation, taxonomies, shared persistence,
  bulk actions, and taxonomy-backed public presentation.
- Remaining debt: `projects.category` still exists as compatibility fallback in
  persistence/runtime surfaces. Admin authoring is removed, but the field still
  needs a deliberate retirement/backfill decision.

### Forms

- Contract fit: aligned as settings-owned.
- Forms have admin CRUD, block-builder module categories, public preview, and
  throttled public submission with idempotency support.
- Ownership decision: forms remain settings-owned because form definitions can
  affect public data capture behavior and submission configuration.
- FEA-017 added dedicated create/update FormRequests with `manage-settings`
  authorization.

### Templates

- Contract fit: aligned as settings-owned.
- Templates have admin CRUD, block-builder template slots, revision restore,
  system-template delete protection, and public layout resolution.
- Ownership decision: templates remain settings-owned because they control
  shared layout and system rendering behavior.
- FEA-017 added dedicated create/update FormRequests with `manage-settings`
  authorization.

## Follow-Up Queue

1. FEA-018: Audit remaining `projects.category` persistence/runtime fallback
   surfaces and decide retirement or explicit long-term compatibility.
2. FEA-012: Normalize residual legacy docs after module ownership decisions are
   recorded.

## Verification

- `php artisan test --filter=PublicRouteContractTest`
- `php artisan test --filter=FormManagementTest`
- `php artisan test --filter=TemplateManagementTest`
- Code reads:
  - `routes/admin.php`
  - `routes/public.php`
  - `app/Http/Controllers/Admin/*Controller.php`
  - `app/Models/Page.php`
  - `app/Models/Post.php`
  - `app/Models/Project.php`
  - `app/Models/Form.php`
  - `app/Models/Template.php`
  - `app/Http/Requests/Admin/*`
  - `config/block_builder.php`
