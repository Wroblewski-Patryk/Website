# Szablony (Templates)

## Typy szablonow

Aktualnie wspierane typy:

- `header`
- `footer`
- `sidebar`
- `page`

## Dane

- `name`
- `type`
- `content` (JSON, bloki)
- `is_active`, `is_default`
- pola SEO

## Uzycie

- Strony moga miec `template_id` oraz override dla `header/footer/sidebar`.
- Posts i projekty korzystaja z tego samego ekosystemu szablonow po stronie buildera/rendrowania.
- Nawigacja i elementy layoutu sa skladane blokowo w szablonach.