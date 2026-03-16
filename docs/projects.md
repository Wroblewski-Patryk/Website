# Moduł projektów (stan kodu)

## Dane i admin

- Model: `Project`.
- Model: `Project`.
- Treść projektu jest blokowa (`content` JSON), zarządzana przez `BlockBuilder`.
- Relacja: `Project` należy do `Client` (`client_id`). W panelu admina dostępna jest lista wyboru klientów.
- Dodatkowe pola: `desktop_image`, `mobile_image`, `url`, `completion_date`.
- Pola SEO i status publikacji (Draft, Published, Planned) są wspierane i translatowalne.

Admin ma pełny CRUD dla `projects` wykorzystujący `BaseAdminContentController` dla spójności logiki.

## Uwaga o slug

- `slug` jest teraz polem translatowalnym (JSON) w tabeli `projects`.
- Logika wyszukiwania projektu po slug wspiera locale (`slug->{locale}`).

## Powiązania

Każdy projekt może być przypisany do klienta w celu organizacji portfolio.