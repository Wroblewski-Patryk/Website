# Model danych (aktualny kod)

## Kluczowe tabele

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

Uwaga: logika routingu projektow jest juz przygotowana pod slug translatable (`slug->{locale}`), ale schema `projects.slug` nadal jest stringiem.

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

### `translations` i `languages`
- `translations`: `group`, `key`, `text` (json)
- `languages`: `code`, `name`, `is_default`, `is_active`

### pozostale
- `settings` (key-value)
- `revisions` (morph)
- `media`, `media_folders`
- `contact_messages`
- `menus` (tabela historyczna, bez aktywnego modulu CRUD)

### `roles` i `permissions` (Spatie)
- `roles`: `name`, `guard_name`
- `permissions`: `name`, `guard_name`
- `model_has_roles`, `role_has_permissions`: Tabele powiązań (RBAC)
- Model `User` posiada trait `HasRoles`.

## Istotna uwaga o zgodnosci domenowej

W `routes/admin.php` sa trasy `categories` i `clients`, ale w aktualnym kodzie brak odpowiadajacych modeli/migracji/kontrolerow.