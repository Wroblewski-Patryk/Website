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

## Automatyzacja i synchronizacja (i18n:scan)

System wspiera automatyczne skanowanie plików źródłowych (`resources/js`, `resources/views`) w celu wykrycia nowych kluczy tłumaczeń.

### Skanowanie
Komenda skanująca:
`php artisan i18n:scan --scope=admin`

Działanie skanera:
1. Wykrywa użycia `t('admin.key', 'Default text')` lub `__('admin.key')`.
2. **Normalizacja**: Klucze z prefiksem `admin.` są zapisywane w bazie z grupą `admin` i kluczem bez prefiksu (np. `admin.common.save` -> group: `admin`, key: `common.save`).
3. Zapobiega duplikatom i automatycznie dodaje brakujące wpisy do tabeli `translations`.

### Weryfikacja integralności
Przed każdym commitem należy uruchomić test integralności:
`php artisan test tests/Feature/Admin/TranslationIntegrityTest.php`

Test ten sprawdza, czy wszystkie klucze wykryte w kodzie znajdują się już w bazie danych/seederach. Jeśli test zawiedzie, oznacza to, że należy zaktualizować pliki w `database/seeders/data/translations/`.

## Aktualne ograniczenia (Zaktualizowane)

- Frontendowy `LanguageSwitcher.vue` pobiera teraz aktywne języki z `page.props.languages`.
- Większość modułów (Pages, Posts, Projects, Clients) korzysta już z pełnej translatowalności pól JSON.