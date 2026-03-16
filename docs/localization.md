# Lokalizacja i tlumaczenia (stan kodu)

## Routing i middleware locale

- Trasy auth/admin/public dzialaja pod prefiksem `/{locale}`.
- `LocaleMiddleware` ustala jezyk w kolejnosci:
  1. parametr trasy `locale`
  2. pierwszy segment URL
  3. sesja
  4. `config('app.locale')`
- Middleware waliduje locale na podstawie aktywnych jezykow z tabeli `languages`.
- Middleware ustawia `URL::defaults(['locale' => ...])`.

## Przelaczanie jezyka

- Trasa switchera: `GET /lang/{lang}` (nazwa: `lang.switch`).
- `LocaleController` akceptuje tylko jezyki aktywne z tabeli `languages`.

## Shared i18n przez Inertia

`HandleInertiaRequests` udostepnia:

- `locale`
- `languages` (aktywne jezyki)
- `translations` (flat mapa `group.key -> text`)

## Zarządzanie tłumaczeniami panelu admina (`AdminUiTranslationSeeder`)

Tłumaczenia interfejsu administracyjnego są przechowywane w bazie danych (tabela `translations`), ale ich źródłem prawdy są pliki PHP w katalogu:
`database/seeders/data/translations/`

Pliki są podzielone tematycznie (np. `menu.php`, `clients.php`, `projects.php`, `common.php`). 
Seeder `AdminUiTranslationSeeder` automatycznie skanuje ten katalog i aktualizuje wpisy w bazie danych.

Wszystkie klucze tłumaczeń dla panelu admina powinny być poprzedzone prefiksem `admin.`.

## Aktualne ograniczenia (Zaktualizowane)

- Frontendowy `LanguageSwitcher.vue` ma obecnie hardcoded opcje `pl` i `en`.
- W dokumentacji i kodzie trwają dalsze porządki i18n po refaktorze.
- Większość modułów (Pages, Posts, Projects, Clients) korzysta już z pełnej translatowalności pól JSON.