# Ustawienia globalne

Modul trzyma konfiguracje jako key-value w tabeli `settings`.

## Klucze uzywane obecnie w kodzie

### Routing/strony

- `home_page_id`
- `blog_page_id`
- `projects_page_id`
- `page_404_id`
- `coming_soon_page_id`
- `maintenance_mode`
- `maintenance_page_id`

### SEO

- `site_name`
- `title_separator`
- `title_order`
- `default_meta_description`
- `default_og_image`
- `sitemap_enabled`
- `robots_disallow_admin`

### Theme/layout

- `theme_config`
- `default_header_id`
- `default_footer_id`

## Uwagi

- Wartosci sa serializowane jako JSON/array.
- Aktualizacja odbywa sie przez panel `admin/settings`.