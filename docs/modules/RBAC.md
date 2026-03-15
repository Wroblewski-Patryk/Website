# Moduł: RBAC (Role-Based Access Control)

## Cel modułu
Zapewnienie bezpiecznego i elastycznego zarządzania uprawnieniami w aplikacji Featherly. System pozwala na definiowanie ról, przypisywanie im uprawnień oraz dynamiczne ograniczanie dostępu do modułów administracyjnych.

## Odpowiedzialność
- Przechowywanie definicji ról i uprawnień.
- Weryfikacja dostępu (Gate/Middleware) na poziomie backendu.
- Filtrowanie elementów interfejsu (sidebar, akcje) na poziomie frontendu.
- Zarządzanie powiązaniami użytkownik–rola.

## Powiązania z innymi modułami
- **User Management**: Użytkownicy są przypisani do ról.
- **Admin UI / Sidebar**: Ukrywa opcje menu na podstawie uprawnień.
- **API / Inertia**: Przesyła mapę uprawnień (`auth.permissions`) do frontendu przez `HandleInertiaRequests`.

## Struktura danych
System opiera się na paczce `spatie/laravel-permission`:
- `roles`: Tabela ról (np. admin, editor).
- `permissions`: Tabela atomowych uprawnień (np. `view-admin`, `manage-content`).
- `model_has_roles`: Powiązanie użytkowników z rolami.
- `role_has_permissions`: Powiązanie ról z uprawnieniami.

Dodatkowo model `User` zachowuje kolumnę `role` (string) dla wstecznej kompatybilności (zsynchronizowana z pierwszą rolą Spatie).

## Opis działania
1. **Middleware**: Trasy admina są chronione przez middleware `permission:name` lub `role:name`.
2. **Super-Admin**: Rola `admin` automatycznie posiada wszystkie uprawnienia (bypass przez `Gate::before` w `AppServiceProvider`).
3. **Frontend**: Komponenty Vue korzystają z composable `usePermission` (lub sprawdzają `$page.props.auth.permissions`), aby decydować o renderowaniu elementów.
4. **Zarządzanie**: Moduł w panelu admina (`Użytkownicy i Role > Role`) pozwala na wizualną edycję uprawnień ról.

## Standardy implementacji
- Każde nowe uprawnienie powinno być dodane do seederów (`SpatieRoleSeeder`).
- Nazewnictwo uprawnień: `kategoria-akcja` (np. `pages-create`, `settings-view`).
- Nazewnictwo ról: małe litery, bez spacji (`editor`, `social-media-manager`).
