# Moduł projektów (stan kodu)

## Dane i admin

- Model: `Project`.
- Treść projektu jest blokowa (`content` JSON), analogicznie do pages/posts.
- Relacja: `Project` należy do `Client` (`client_id`).
- Dodatkowe pola: `desktop_image`, `mobile_image`, `url`, `category`, `order`.
- Pola SEO i status publikacji są wspierane i translatowalne.

Admin ma pełny CRUD dla `projects` zintegrowany z `BlockBuilder`.

## Uwaga o slug

- `slug` jest teraz polem translatowalnym (JSON) w tabeli `projects`.
- Logika wyszukiwania projektu po slug wspiera locale (`slug->{locale}`).

## Powiązania

Każdy projekt może być przypisany do klienta w celu organizacji portfolio.