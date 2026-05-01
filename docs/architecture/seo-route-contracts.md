# SEO Route Contracts and Locale Behavior

## Purpose
This document defines stable routing and locale contracts for public SEO behavior (canonical URLs, alternate locale URLs, localized route prefixes, and fallback semantics).

## Public Route Topology

### Technical endpoints (non-localized)
- `GET /sitemap.xml` -> `SeoController@sitemap`
- `GET /robots.txt` -> `SeoController@robots`
- `GET /lang/{lang}` -> `LocaleController@switch`

### Locale-prefixed public content
Public content is served under `/{locale}` groups loaded from `routes/public.php`:
- `GET /{locale}` -> home (`home` -> `PageController@show`)
- `GET /{locale}/{path}` -> named dynamic content resolver (`public.content.show` -> `PageController@show`)
- The resolver route is intentionally shared by pages, blog post detail paths,
  and project detail paths so archive page settings remain the authoritative
  source of URL shape.

### Non-prefixed fallbacks and redirects
`FallbackController` normalizes non-prefixed paths into localized equivalents:
- `/` -> `/{active-locale}`
- `/login` -> localized login route
- `/admin` and `/dashboard/*` -> localized admin URLs
- any unmatched non-prefixed content path -> `/{active-locale}/{path}`

## Locale Resolution Contract

### Middleware precedence
`LocaleMiddleware` resolves locale in this order:
1. route parameter `locale`
2. first path segment
3. session locale
4. `config('app.locale')`

If the resolved locale is not active, middleware falls back to `config('app.locale')`.

### Session behavior
- Valid locale requests update session locale.
- Locale switch endpoint (`/lang/{lang}`) rejects inactive locales with HTTP `400`.

### Content fallback behavior
For translatable slugs in public resolvers, lookup uses:
1. current app locale
2. `config('app.fallback_locale')`

This applies to:
- direct page resolution
- blog archive + post detail tail resolution
- projects archive + project detail tail resolution

## Dynamic Public Route Resolution

`PageController@show` contract:
1. Resolve current locale and requested path.
2. Handle root as configured home page.
3. Handle taxonomy paths (`/category/{slug}`, `/tag/{slug}`).
4. Resolve exact page by localized/fallback slug (full path allowed, including nested slugs).
5. For multi-segment paths, resolve first segment as archive page:
   - blog archive -> delegate tail to `PostController@show`
   - projects archive -> delegate tail to `ProjectController@show`
6. If no match, return configured custom 404 page or standard 404.

### Regression Coverage
- `tests/Feature/PublicRouteContractTest.php` verifies the named localized
  entrypoints for home, page detail, post detail, and project detail requests.

## Approved V1 Taxonomy Direction

- Module-scoped taxonomies are the canonical semantic-grouping system for
  `posts` and `projects`.
- V1 public taxonomy archive URLs remain a blog surface and are reserved for
  the `posts` module only.
- `projects.category` is a legacy compatibility field and is not the long-term
  semantic-grouping contract.
- Runtime guard: public taxonomy resolution must fail closed for any taxonomy
  outside the `posts` module, even when type and localized slug match.

## SEO Metadata Contract

`SeoService::getMetaData()` provides:
- `title`
- `full_title`
- `description`
- `og_image`
- `robots`
- `canonical`
- `alternate_locales`

### Canonical URL
- Uses entity `canonical_url` when present.
- Validation/normalization is handled in admin write paths.

### Alternate locales (`hreflang`)
- Generated for active locales only.
- Supports nested slugs.
- Includes archive prefix for post/project entities using configured archive pages.
- Includes `x-default` pointing to default active locale URL.
- Rendered in head as `<link rel="alternate" hreflang="...">`.

## Sitemap and Robots Contract

### Sitemap
- Controlled by `sitemap_enabled` setting.
- Includes published/indexable pages, posts, and projects.
- Deduplicates by normalized path.

### Robots
- Controlled by `robots_disallow_admin` and `sitemap_enabled`.
- Emits sitemap URL only when sitemap is enabled.

## Guardrails for Future Changes
- Keep route prefixing and fallback redirects consistent with this contract.
- Do not introduce mixed locale URL formats for public content.
- Maintain parity between resolver behavior and generated `alternate_locales`.
- Do not expose project taxonomy archives publicly through `/category/{slug}` or
  `/tag/{slug}` without an approved module-qualified route contract.
- Any route/locale contract change must update this file and associated test coverage.
