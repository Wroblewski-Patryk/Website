# Moduł klientów (Clients)

## Cel modułu
Moduł służy do zarządzania listą klientów, dla których realizowane były projekty. Pozwala na przechowywanie danych kontaktowych, logotypów (opcjonalnie) oraz powiązanie z konkretnymi realizacjami w portfolio.

## Odpowiedzialność
- Przechowywanie informacji o klientach (nazwa, opis, url).
- Zarządzanie statusem aktywności klienta.
- Udostępnianie listy klientów dla modułu Projektów.

## Struktura danych
- Model: `Client`.
- Pola translatowalne (JSON): `title`, `slug`, `description`.
- Pola techniczne: `email`, `phone`, `website_url`, `is_active`.

## Powiązania
- `Client` hasMany `Project`.

## UI / Admin
- Lista klientów z filtrowaniem i sortowaniem.
- Formularz edycji wspierający wielojęzyczność dla pól tekstowych.
