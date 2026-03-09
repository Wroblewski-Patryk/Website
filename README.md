# Featherly CMS

Custom CMS oparty o Laravel 12 + Vue 3 + Inertia, z wizualnym edytorem blokowym.

## Uruchamianie lokalnie

Mozesz pracowac na dwa sposoby:

1. Dwa terminale:
- `php artisan serve`
- `npm run dev`

2. Jeden terminal:
- `composer run dev`

`composer run dev` uruchamia caly zestaw developerski (server, queue listener, logi i Vite).

## Stan projektu (HEAD)

- Dziala panel admina: pages, posts, projects, forms, templates, media, settings, translations, languages, users, theme.
- Dziala publiczny routing dla stron, bloga, projektow i preview formularzy.
- Dziala SEO (`sitemap.xml`, `robots.txt`, SEO meta per model).
- Czesci i18n sa w trakcie dalszych prac.

## Uwagi

- Modul menu jako osobna sekcja admina nie jest aktywny.
- Nawigacja jest budowana przez bloki i szablony.
- Runtime submit dla dynamicznych formularzy jest do dopiecia w kolejnym etapie.