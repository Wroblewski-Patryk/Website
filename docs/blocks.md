# Bloki tresci (stan HEAD)

Glowne grupy w builderze:

- Typography: `paragraph`, `heading`, `list`, `quote`, `custom_code`
- Actions: `button`, `dropdown`, `modal`, `swap`
- Data display: `accordion`, `avatar`, `badge`, `card`, `carousel`, `chat`, `countdown`, `diff`, `stat`, `table`, `timeline`
- Feedback: `alert`, `progress`, `radial_progress`
- Data input: `text_input`, `textarea`, `select`, `checkbox`, `radio`, `toggle`, `range`, `rating`, `file_input`
- Layout & media: `container`, `divider`, `hero`, `image`, `video`
- Navigation: `breadcrumbs`, `menu`, `navbar`, `steps`, `tabs`
- Mockup: `mockup_browser`, `mockup_code`, `mockup_phone`, `mockup_window`
- Building: `template_reference`, `content_slot`
- Extended: `posts_list`, `projects_list`, `text_rotate`

## Struktura bloku

Kazdy blok zawiera m.in.:

- `id`
- `type`
- `content`
- `children`
- `settings` (layout/style/animations)

## Uwagi

- Nawigacja i elementy layoutu sa budowane blokowo (nie przez osobny modul menu).
- Animacje w builderze sa przygotowane strukturalnie; ich pelny runtime jest rozwijany osobno.