# Featherly CMS

Featherly is a custom CMS built with Laravel 12, Inertia, and Vue 3.  
It focuses on block-based content editing, multilingual content, and a custom admin panel.

## Local Development

Use one of these options:

1. Two terminals:
- `php artisan serve`
- `npm run dev`

2. One terminal:
- `composer run dev`

`composer run dev` starts server, queue, pail, and Vite together.

## Current Routing Architecture

Routing is modular and localized:

- `routes/auth.php` under `/{locale}`
- `routes/admin.php` under `/{locale}/admin`
- `routes/public.php` under `/{locale}`
- technical routes without locale prefix: `sitemap.xml`, `robots.txt`, `lang/{lang}`

Locale behavior is wired in `bootstrap/app.php` and locale middleware.

## Current Status (2026-05-02)

Implemented:

- Admin modules: pages, posts, media, projects, forms, templates, translations, languages, users, settings, theme, blocks, clients.
- i18n backend: `languages` + `translations`, shared to frontend via Inertia props.
- SEO base: `sitemap.xml`, `robots.txt`, canonical/alternate locale behavior, and default meta handling.
- Translation scanning workflow: `php artisan i18n:scan --scope=admin` + integrity test.
- Public dynamic routes for home, pages, blog posts, project detail, and project archive through localized named routes.
- Posts-only public taxonomy archives for V1.
- Taxonomy-first project category presentation with `projects.category` retained as a read-only compatibility fallback.
- System Update Manager v1 local contracts for manual, Coolify, and archive update flows.

Known gaps and blockers:

- Coolify production update enablement still requires captured staging/live rollout evidence in the target environment.
- `projects.category` column removal requires a later data inventory/backfill/removal plan.
- The fuller `/api/v1/headless/*` contract remains target architecture; the implemented read integration route is `/headless/content-export`.

## Documentation

Primary docs are in `docs/`.
Start from:

- `docs/overview.md`
- `docs/product/product.md`
- `docs/architecture/current-implementation-map.md`
- `docs/architecture/system-architecture.md`
- `docs/planning/mvp-next-commits.md`
