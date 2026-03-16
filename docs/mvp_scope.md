# Zakres MVP (aktualny stan kodu)

## Panel admin

Sekcje aktywne i routowane:

1. Auth
2. Dashboard
3. Pages
4. Posts
5. Media
6. Projects
7. Forms
8. Templates
9. Translations
10. Languages
11. Users
12. Settings
13. Theme
14. Blocks
15. Clients

Sekcje zarejestrowane routami, ale niezamkniete implementacyjnie:

- Categories (do domknięcia jako taksonomie)
- Mediatheque (rozszerzenie Media)

## Publiczny runtime (obecnie wystawione trasy)

- `GET /{locale}`
- `GET /{locale}/forms/{id}/preview`
- `GET /sitemap.xml`
- `GET /robots.txt`
- `GET /lang/{lang}`

## W toku

- Finalne wystawienie dynamicznych routow page/blog/project.
- Domkniecie submit runtime dla formularzy.