# Headless Read API Contract (Public Content)

## Purpose
Define a stable, versioned, read-only API contract for external consumers (frontend apps, integrations, automation), without changing current public web routing.

## Scope
- Read-only.
- Public content only (`pages`, `posts`, `projects`).
- Locale-aware responses.
- No write endpoints.

## Base Path and Versioning
- Base path: `/api/v1/headless`
- Versioning rule: breaking changes require a new major path (`/api/v2/...`).

## Transport and Format
- `Content-Type`: `application/json; charset=utf-8`
- Datetime format: ISO-8601 (`YYYY-MM-DDTHH:mm:ssZ`)
- Locale fields:
  - `locale`: resolved request locale
  - `fallback_locale`: app fallback locale

## Resource Endpoints

### 1) Content list
- `GET /api/v1/headless/content`
- Query params:
  - `type` (`page|post|project`, optional)
  - `locale` (optional)
  - `status` (optional, default `published`)
  - `per_page` (optional, default `20`, max `100`)
  - `cursor` (optional)
- Response contract:
  - `data[]`: normalized content items
  - `meta`: pagination + locale metadata
  - `links`: `next`, `prev`

### 2) Single content by ID
- `GET /api/v1/headless/content/{type}/{id}`
- Params:
  - `type` (`page|post|project`)
  - `id` numeric
- Response contract:
  - `data`: normalized content item
  - `meta`: locale metadata

### 3) Single content by localized slug path
- `GET /api/v1/headless/content/{type}/slug/{slugPath}`
- `slugPath` supports nested path segments.
- Query params:
  - `locale` (optional)
- Response contract:
  - `data`: normalized content item
  - `meta`: locale metadata

## Normalized Item Schema
```json
{
  "id": 123,
  "type": "page",
  "status": "published",
  "title": "Localized title",
  "slug": "localized-slug-or-path",
  "excerpt": null,
  "content": {},
  "published_at": "2026-03-25T10:00:00Z",
  "updated_at": "2026-03-25T10:15:00Z",
  "seo": {
    "title": "Meta title",
    "description": "Meta description",
    "canonical": "https://example.com/en/path",
    "robots": "index,follow"
  },
  "links": {
    "web": "https://example.com/en/path"
  }
}
```

## Filtering and Visibility Rules
- Default visibility: `published` only.
- Planned/draft visibility is disallowed on public tokenless endpoints.
- Archived content is excluded.
- Missing locale value must fallback to configured fallback locale.

## Error Envelope
All non-2xx responses use:
```json
{
  "error": {
    "code": "resource_not_found",
    "message": "Content not found.",
    "details": {}
  }
}
```

### Status code contract
- `400` invalid params
- `401` unauthorized (future token-scoped mode)
- `403` forbidden
- `404` resource not found
- `422` validation/shape error
- `429` rate limited
- `500` internal error

## Caching Contract
- Response headers:
  - `Cache-Control` with TTL profile by content type
  - `ETag`
  - `Last-Modified`
- Cache invalidation must happen on publish/unpublish/update of affected records.

## Security Baseline
- Read-only scope.
- Rate limiting per IP/token.
- Input normalization for `locale`, `type`, and `slugPath`.
- No exposure of internal admin-only fields.

## Compatibility Rules
- Additive fields are allowed in `data` and `meta`.
- Existing fields cannot change type/meaning in `v1`.
- Deprecated fields must be announced before removal in next major version.

## Out of Scope (Follow-up Tasks)
- Token-scoped access model (`SCL-056`).
- Export endpoint and bulk contract (`SCL-057`).
- Webhook/event stream for content changes.
