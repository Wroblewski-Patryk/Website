# Modules

## Module List
- Installer
  - Owner: ops/backend bootstrap
  - Responsibilities: first-run installation guard, database connection check,
    default language selection, migration/seeding finalize, installer lock
  - Inputs/Outputs: setup form payloads -> initialized environment state
- Block Builder
  - Owner: admin frontend
  - Responsibilities: visual authoring, block state, shortcuts, autosave,
    conflict prompts, history, responsive design controls, animation settings,
    media picker fields, and block configuration
  - Inputs/Outputs: content JSON + settings JSON + optimistic lock timestamp
- Renderer
  - Owner: frontend runtime
  - Responsibilities: resolve block types, template references, composed block
    references, animations, icons, public project lists, and runtime UI
  - Inputs/Outputs: block JSON -> rendered components
- Content Architecture
  - Owner: backend content domain
  - Responsibilities: shared behavior for page/post/project CRUD, validation,
    policies, transactions, revisions, bulk actions, slug rules, SEO fields,
    and taxonomy sync
  - Inputs/Outputs: normalized model behavior, admin Inertia responses, JSON
    bulk/export responses
- Forms
  - Owner: content/runtime backend + frontend
  - Responsibilities: form definitions, preview, throttled runtime submissions,
    idempotency-key handling
  - Inputs/Outputs: form JSON definitions, submission records, duplicate-safe
    submit responses
- Composed Blocks
  - Owner: block-builder domain
  - Responsibilities: reusable user-defined multi-block compositions, active
    library sharing, optimistic-lock updates, revision snapshots
  - Inputs/Outputs: composed block definitions (`title`, `slug`, `content`, `settings`, `is_active`)
- Animation Presets
  - Owner: block-builder/theme runtime domain
  - Responsibilities: reusable GSAP animation definitions and active preset
    library sharing
  - Inputs/Outputs: preset records (`name`, `slug`, `definition`, `is_active`)
- Projects
  - Owner: content domain
  - Responsibilities: project portfolio entities with client relation,
    taxonomy-backed public category presentation, legacy category fallback, SEO
    fields, and locale-aware slug resolution
  - Inputs/Outputs: project records + client relation + taxonomy relations +
    public presenter payload
- Clients
  - Owner: project content domain
  - Responsibilities: project client CRUD and project relationship ownership
  - Inputs/Outputs: client records linked by `projects.client_id`
- Taxonomies
  - Owner: content classification domain
  - Responsibilities: module-scoped categories and tags for posts/projects,
    ordered admin management, fail-closed public post taxonomy archives
  - Inputs/Outputs: taxonomy records + polymorphic taxonomy relations
- Templates
  - Owner: layout domain
  - Responsibilities: reusable page/header/footer/sidebar structures, system
    page templates, template reference resolution, and page/header/footer
    override behavior
  - Inputs/Outputs: template JSON blocks + global associations
- Settings
  - Owner: configuration domain
  - Responsibilities: global key-value configuration for routing, SEO, theme,
    builder autosave interval, default templates, brand assets, system pages,
    sitemap and robots behavior
  - Inputs/Outputs: key-value settings consumed by runtime/admin
- Media
  - Owner: media domain
  - Responsibilities: upload, listing, folders, picker modal, asset references,
    MIME validation, checksum deduplication, safe replacement, lifecycle
    retention/purge metadata
  - Inputs/Outputs: media records + folder records + storage files + URL
    accessor
  - Contracts:
    - `docs/architecture/media-picker-control-contract.md`
- RBAC
  - Owner: authz/security domain
  - Responsibilities: roles/permissions and access enforcement
  - Inputs/Outputs: permission map to backend middleware and frontend props
- Admin Search
  - Owner: admin productivity domain
  - Responsibilities: provider-based global search across base content modules
  - Inputs/Outputs: query string -> normalized ranked search result envelope
- Headless Export
  - Owner: delivery/integration domain
  - Responsibilities: token-scoped JSON export of public page/post/project
    content
  - Inputs/Outputs: bearer token + filters -> paginated JSON export envelope
- Operations
  - Owner: reliability/ops domain
  - Responsibilities: scheduled publishing, media lifecycle command,
    performance smoke checks, operational metrics, health checks, response
    budgets, request correlation, slow query profiling
  - Inputs/Outputs: artisan commands, scheduler entries, logs, JSON health and
    metrics output

## Cross-Module Contracts
- Locale-aware routes and translatable fields are required across content modules.
- Block content contracts must stay compatible between builder and renderer.
- Module-scoped block palette contract:
  - `docs/architecture/module-block-registry-contract.md`
- ContentType module contract:
  - `docs/architecture/content-type-module-contract.md`
- Builder design controls contract:
  - `docs/architecture/builder-design-controls-contract.md`
- Settings and templates must remain compatible with public runtime rendering.
- Role/permission decisions must be mirrored in admin UI visibility.
- Public project category payloads must prefer `projects` taxonomy categories
  and use `projects.category` only as compatibility fallback until removed.
- Admin write paths for publishable content must preserve validation,
  authorization, canonical URL normalization, optimistic locking, transactional
  persistence, revision snapshots, and taxonomy synchronization.
- Shared Inertia payload additions must use cache-aware ownership and avoid
  restoring heavy global props without explicit architecture review.
