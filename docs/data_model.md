# Model danych (stan HEAD)

## Najwazniejsze tabele

### `pages`
- `status`
- `title` (json)
- `slug` (json)
- `content` (json)
- `settings` (json)
- `published_at`, `archived_at`
- `template_id`, `header_override_id`, `footer_override_id`, `sidebar_override_id`
- SEO: `meta_title` (json), `meta_description` (json), `og_image` (json), `canonical_url`, `seo_index`, `seo_follow`

### `posts`
- `status`
- `title` (json)
- `slug` (json)
- `excerpt` (json)
- `content` (json)
- `featured_image` (json)
- `published_at`, `archived_at`
- SEO: `meta_title` (json), `meta_description` (json), `og_image` (json), `canonical_url`, `seo_index`, `seo_follow`

### `projects`
- `status`
- `title` (json)
- `slug` (string)
- `description` (json)
- `content` (json)
- `desktop_image`, `mobile_image`
- `url`, `category`, `client`, `order`
- `published_at`, `archived_at`
- SEO: `meta_title` (json), `meta_description` (json), `og_image` (json), `canonical_url`, `seo_index`, `seo_follow`

### `forms`
- `status`
- `title`
- `content` (json)
- `settings` (json)
- `published_at`, `archived_at`

### `templates`
- `name`
- `type` (`header`, `footer`, `sidebar`, `page`)
- `is_active`, `is_default`
- `content` (json)
- SEO: `meta_title` (json), `meta_description` (json), `og_image` (json), `canonical_url`, `seo_index`, `seo_follow`

### Pozostale
- `media`, `media_folders`
- `translations`, `languages`
- `settings` (key-value)
- `revisions` (morph)
- `contact_messages`
- `menus` (tabela istnieje, ale modul nie jest aktywny)

## Relacje

- `pages`, `posts`, `templates` -> `morphMany(revisions)`
- `pages` -> `belongsTo(templates)` przez pola override/template
- `media` -> `belongsTo(media_folders)`
- `media_folders` -> self-tree (`parent`, `children`)