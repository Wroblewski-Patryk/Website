# Prompt dla antigravity - wdrozenie pelnego auth + role + permissions + maile konta

## Jak uzyc
Wklej sekcje **PROMPT START -> PROMPT END** 1:1 do antigravity.

## PROMPT START
Jestes senior Laravel 12 + Inertia/Vue engineer.
Pracujesz w repo Featherly CMS.
Twoim celem jest wdrozenie pelnego auth "jak w WordPress": role/capabilities, ochrona akcji modulow oraz maile konta.

### Zasady wykonania
1. Pracuj etapami (oddzielne PR-y), nie rob jednego wielkiego diffa.
2. Kazdy etap ma miec:
- zmiany kodu,
- testy feature,
- krotki changelog i instrukcje uruchomienia.
3. Nie psuj obecnego panelu admina i routingu locale (`/{locale}/admin/...`).
4. Zachowaj obecny stack: Laravel native auth + Inertia, bez migracji do Breeze/Jetstream.
5. Permission naming ma byc: `<module>.<action>`.

### Kolejnosc etapow (obowiazkowa)

#### Etap 0 - Stabilizacja tras
- Sprawdz i napraw niespojnosc `admin.categories.*` i `admin.clients.*`:
  - dodaj brakujace kontrolery i minimum widokow, ALBO
  - usun trasy tymczasowo, jesli modul nie jest gotowy.
- Upewnij sie, ze route:list dziala i nie ma martwych endpointow.

DoD:
- `php artisan route:list` przechodzi bez bledow.

#### Etap 1 - RBAC foundation
- Zainstaluj i skonfiguruj `spatie/laravel-permission`.
- Dodaj migracje package + uruchom mapowanie na guard `web`.
- Dodaj `HasRoles` do `App\\Models\\User`.
- Dodaj `config/permissions.php` z centralna lista modulow i akcji.
- Dodaj seeder `RolesAndPermissionsSeeder` z rolami:
  - super-admin
  - administrator
  - editor
  - author
  - translator

DoD:
- Seeder tworzy role i permission.
- User z rola ma poprawne `can()`.

#### Etap 2 - Backend enforcement
- Dodaj middleware mapujace route name do permission, np.:
  - `admin.pages.index` -> `pages.view`
  - `admin.pages.store` -> `pages.create`
  - `admin.pages.update` -> `pages.update`
  - `admin.pages.destroy` -> `pages.delete`
- Podepnij middleware do grupy `admin`.
- Dodaj bypass dla `super-admin`.
- Dla krytycznych modulow (`users`, `settings`, `languages`, `translations`) dodaj dodatkowe twarde sprawdzenia `can(...)`/Policy.
- Dodaj obsluge 403 dla Inertia (czytelna strona "Brak uprawnien").

DoD:
- Bez permission endpoint zwraca 403.
- Z permission endpoint dziala.

#### Etap 3 - Frontend permission-aware
- W `HandleInertiaRequests` dodaj:
  - `auth.roles`
  - `auth.permissions`
- Dodaj composable `useAbility()` lub helper `can(permission)`.
- Ukryj w UI elementy bez uprawnien:
  - pozycje sidebar,
  - przyciski Create/Edit/Delete,
  - akcje tabel i formularzy.
- Dodaj komunikaty UX dla zablokowanych akcji.

DoD:
- UI odzwierciedla role i prawa.
- Backend nadal jest zrodlem prawdy (UI to tylko UX).

#### Etap 4 - Auth lifecycle + maile konta
- Wdrozenie flow:
  - forgot password (formularz requestu),
  - reset password (token + nowe haslo),
  - weryfikacja email dla nowych kont (`MustVerifyEmail`).
- Refactor tworzenia usera:
  - admin nie ustawia recznie finalnego hasla,
  - user dostaje secure link (invite / set-password via broker).
- Dodaj notyfikacje email (queued):
  - account created/invite,
  - password reset,
  - password changed confirmation,
  - optional: role changed.
- Dodaj blade templates maili i sensowne subjects.

DoD:
- Wszystkie flow konta dzialaja end-to-end.
- Maile testowane przez `Mail::fake()` / `Notification::fake()`.

#### Etap 5 - Security hardening
- Dodaj rate-limit dla login i forgot-password.
- Dodaj polityke hasla (min length + ewentualnie complexity).
- Zabezpiecz self-lockout (np. brak mozliwosci usuniecia ostatniego admina).
- Dodaj audit log dla krytycznych akcji konta (opcjonalnie tabela `audit_logs`).

DoD:
- Brak regresji logowania i sesji.
- Krytyczne akcje sa chronione i udokumentowane.

#### Etap 6 - Testy i rollout docs
- Dodaj test matrix: rola x modul x akcja (allow/deny).
- Dodaj testy flow auth + mail.
- Zaktualizuj dokumentacje wdrozeniowa:
  - migracje,
  - seed,
  - konfiguracja SMTP,
  - smoke test po deployu.

DoD:
- Zielone testy krytyczne.
- Gotowa instrukcja release.

### Minimalny zakres permissions (v1)
- dashboard.view
- pages.view/create/update/delete/publish
- posts.view/create/update/delete/publish
- projects.view/create/update/delete/publish
- forms.view/create/update/delete
- media.view/create/update/delete
- templates.view/create/update/delete
- theme.view/update
- blocks.view/update
- settings.view/update
- translations.view/create/update/delete
- languages.view/create/update/delete
- users.view/create/update/delete/manage
- categories.view/create/update/delete
- clients.view/create/update/delete

### Wymagania dot. outputu dla kazdego etapu
W odpowiedzi po wdrozeniu etapu podaj:
1. co zmieniono,
2. lista plikow,
3. komendy migracji/testow,
4. ryzyka/regresje,
5. co zostaje na kolejny etap.

## PROMPT END
