# Plan wdrozenia pelnego Auth + RBAC + Maile konta

## 1) Cel
Wdrozyc "WordPress-like" system uprawnien:
- role -> capability (permission),
- capability -> akcje w modulach admina,
- pelny lifecycle konta (logowanie, reset hasla, weryfikacja email, powiadomienia mailowe),
- bez rozwalania obecnej architektury Laravel 12 + Inertia/Vue.

## 2) Stan obecny (analiza projektu)
- Jest tylko podstawowe logowanie sesyjne (`Auth::attempt`) i `auth` middleware.
- Brak warstwy RBAC (brak `roles`, `permissions`, policies, gates).
- W `HandleInertiaRequests` przekazywany jest user bez roli/uprawnien.
- UI admina pokazuje wszystkie moduly niezaleznie od praw.
- Brak flow "forgot/reset password", brak weryfikacji email w praktyce.
- Mail dla kontaktu jest niedokonczony (`App\\Mail\\ContactMessage` ma placeholder `view.name`).
- Trasy `admin.categories.*` i `admin.clients.*` istnieja, ale brak kontrolerow w repo (do uporzadkowania przed RBAC).

## 3) Decyzja architektoniczna (rekomendacja)
Uzyc `spatie/laravel-permission` jako warstwy RBAC:
- sprawdzone podejscie "role + permission" (jak WP capability model),
- latwa skalowalnosc przy nowych modulach,
- prosty seed i testowanie.

Konwencja permission:
- `<modul>.<akcja>`
- Przyklad: `pages.view`, `pages.create`, `pages.update`, `pages.delete`.

## 4) Slownik akcji
- `view`: lista/podglad/ekrany modulu
- `create`: tworzenie rekordow
- `update`: edycja rekordow i ustawien
- `delete`: usuwanie
- `manage`: akcje administracyjne wysokiego ryzyka (np. role, jezyki, tlumaczenia globalne)
- `publish` (opcjonalnie etap 2): publikacja tresci

## 5) Proponowane role startowe (v1)
- `super-admin`: wszystko (bypass).
- `administrator`: wszystkie moduly biznesowe + system, bez bypass.
- `editor`: pages/posts/projects/forms/media/templates/theme/blocks (bez users/languages/translations/settings krytycznych).
- `author`: pages/posts/projects/forms (bez delete globalnego i bez system settings).
- `translator`: translations + languages.view.

## 6) Moduly i minimalna macierz permission (v1)
- `dashboard`: `view`
- `pages`: `view/create/update/delete/publish`
- `posts`: `view/create/update/delete/publish`
- `projects`: `view/create/update/delete/publish`
- `forms`: `view/create/update/delete`
- `media`: `view/create/update/delete`
- `templates`: `view/create/update/delete`
- `theme`: `view/update`
- `blocks`: `view/update`
- `settings`: `view/update`
- `translations`: `view/create/update/delete`
- `languages`: `view/create/update/delete`
- `users`: `view/create/update/delete/manage`
- `categories`: `view/create/update/delete` (po naprawie modulu)
- `clients`: `view/create/update/delete` (po naprawie modulu)

## 7) Plan etapowy

### Etap 0 - Stabilizacja bazowa (must-have przed RBAC)
Zakres:
- Naprawic niespojnosc tras `clients/categories`:
  - albo dodac kontrolery i widoki,
  - albo tymczasowo usunac trasy z `routes/admin.php`.
- Ujednolicic flash messages (`success/error`) i kody odpowiedzi 403.

Definition of Done:
- `php artisan route:list` dziala bez bledow.
- Brak "martwych" tras bez kontrolera.

### Etap 1 - Fundament RBAC
Zakres:
- Dodac paczke `spatie/laravel-permission`.
- Opublikowac migracje i config.
- W `User` dodac trait `HasRoles`.
- Dodac seeder `RolesAndPermissionsSeeder`.
- Dodac centralny config mapy permissions, np. `config/permissions.php`.

Definition of Done:
- Po `db:seed` role i permission istnieja.
- Mozna przypisac role userowi i odczytac `user->can(...)`.

### Etap 2 - Egzekwowanie permission na backendzie
Zakres:
- Dodac middleware mapujace `route name -> permission`.
- Podpiac middleware do grupy `admin`.
- Dla akcji wysokiego ryzyka (users/settings/languages/translations) dodac twarde `can(...)`/policy.
- Dodac dedykowany response 403 (Inertia page + JSON fallback).

Definition of Done:
- User bez prawa dostaje 403 na route i API endpoint.
- User z prawem przechodzi poprawnie.

### Etap 3 - UI permission-aware (Inertia/Vue)
Zakres:
- W `HandleInertiaRequests` udostepnic `auth.roles` i `auth.permissions`.
- Dodac helper/composable `can()` w frontendzie.
- Ukryc elementy menu i CTA ("Create/Delete/Edit") bez prawa.
- Dodac czytelny komunikat "brak uprawnien" zamiast martwego kliku.

Definition of Done:
- Sidebar i akcje tabel odzwierciedlaja role.
- Brak mozliwosci wykonania akcji bez prawa (UI + backend).

### Etap 4 - Pelny lifecycle konta + maile
Zakres:
- Forgot password + reset password (widoki Inertia + token broker).
- Weryfikacja email (`MustVerifyEmail`) dla nowych kont.
- Powiadomienia mailowe:
  - utworzenie konta/invite,
  - reset hasla,
  - potwierdzenie zmiany hasla,
  - (opcjonalnie) zmiana roli.
- W `UserController@store` usunac wzorzec "ustaw haslo recznie przez admina":
  - tworzyc usera z losowym haslem,
  - wysylac secure link do ustawienia hasla.
- Kolejkowanie maili (`ShouldQueue` + `QUEUE_CONNECTION=database`).

Definition of Done:
- Wszystkie flow konta dzialaja end-to-end.
- Maile wysylane przez queue i testowane `Mail::fake()`.

### Etap 5 - Hardening bezpieczenstwa
Zakres:
- Rate limiting dla login i reset hasla.
- Minimalna polityka hasla (dlugosc + zlozonosc opcjonalnie).
- Ochrona przed self-lockout:
  - user nie moze usunac sam sobie ostatniej roli admin.
- Audyt zdarzen konta (opcjonalnie tabela `audit_logs`).

Definition of Done:
- Brak regresji logowania.
- Krytyczne akcje sa ograniczone i logowane.

### Etap 6 - Testy, dokumentacja, rollout
Zakres:
- Testy feature:
  - matrix: rola x modul x akcja (allow/deny),
  - auth flow (login/forgot/reset/verify),
  - maile i notyfikacje.
- Dokumentacja dla zespolu:
  - jak dodac nowy modul i permission,
  - jak seedowac role.
- Plan wdrozenia:
  - backup DB,
  - migrate + seed,
  - smoke test panelu.

Definition of Done:
- Zielone testy krytycznych scenariuszy.
- Instrukcja operacyjna gotowa dla release.

## 8) Kolejnosc wdrozenia (bezpieczna)
1. Etap 0
2. Etap 1
3. Etap 2
4. Etap 3
5. Etap 4
6. Etap 5
7. Etap 6

## 9) Najwazniejsze ryzyka i jak je obnizyc
- Ryzyko: nagle 403 dla obecnych userow.
  Mitigacja: migracja nadaje `super-admin` pierwszemu adminowi i fallback seed.
- Ryzyko: zepsute flow user creation.
  Mitigacja: wprowadzic "invite flow" dopiero po gotowym reset broker.
- Ryzyko: niedopiete maile produkcyjne.
  Mitigacja: feature flag dla maili konta + test SMTP przed rolloutem.

## 10) KPI po wdrozeniu
- 100% akcji admina przechodzi przez permission check.
- 0 endpointow admina dostepnych tylko przez "ukrycie przycisku".
- 100% flow konta pokryte testami e2e/feature.
