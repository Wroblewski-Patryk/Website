# Product

## Problem
Teams need a CMS that is fast for editors, supports multilingual content, and does not require coupling to a heavy external admin framework.

## Users and Segments
- Admin users (content editors, marketers)
- Technical operators (developers, maintainers)
- End users consuming localized content pages

## Core Jobs To Be Done
- Create and publish pages/posts/projects with structured blocks.
- Manage reusable templates and global settings.
- Keep admin UI translated and locale-aware.
- Maintain SEO basics and route consistency.

## Current Product Baseline
- Localized route model with locale middleware and language switching.
- Public home/page/post/project resolution through localized named routes.
- Block Builder as central editing UX.
- Admin CRUD across core content and configuration modules.
- Translation scan + integrity checks to reduce missing UI keys.

## MVP Features
- Localized routing and language management.
- Block-based content authoring for primary modules.
- Admin operations for media, settings, templates, and users.
- SEO baseline (`sitemap.xml`, `robots.txt`, core meta fields).
- System Update Manager local contracts for safe update discovery and gated
  apply drivers.

## Post-MVP Focus
- Production rollout evidence for environment-specific update drivers.
- Legacy project category fallback backfill/removal.
- Expanded animation tooling and scene/timeline authoring.
- Additional hardening for release and runtime observability.

## Acceptance Criteria Strategy
Each feature is considered complete when:
- behavior is documented,
- route and permission impact is explicit,
- relevant tests or smoke checks were executed,
- follow-up items are captured in planning docs.
