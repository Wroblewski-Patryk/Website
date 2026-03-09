# Zakres MVP (stan faktyczny HEAD)

## Panel admin

Dostepne sekcje:

1. Auth
2. Pages
3. Posts
4. Projects
5. Forms
6. Templates
7. Media (z folderami)
8. Settings
9. Translations
10. Languages
11. Users
12. Theme
13. Blocks

## Publiczny runtime

- `GET /` i `GET /{slug}` dla stron
- `GET /blog/{slug}` dla wpisow
- `GET /projects/{slug}` dla projektow
- `GET /forms/{id}/preview` dla podgladu formularza
- `GET /sitemap.xml`
- `GET /robots.txt`

## Co jeszcze nie jest domkniete

- Osobny modul menu (CRUD) nie jest aktywny.
- Dynamiczny submit formularzy z definicji `forms` jest do implementacji.
- Lokalizacja runtime jest aktualnie rozwijana.