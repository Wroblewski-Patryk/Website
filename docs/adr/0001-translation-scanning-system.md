# ADR 0001: System skanowania i synchronizacji tłumaczeń

## Data
2026-03-17

## Status
Zaakceptowana

## Kontekst
Zarządzanie tłumaczeniami w dynamicznym systemie CMS jest utrudnione, gdy klucze znajdują się zarówno w kodzie (JS/PHP), jak i w bazie danych. Ręczna synchronizacja jest błędogenna i prowadzi do brakujących tłumaczeń na produkcji.

## Decyzja
Wprowadzamy dedykowany system skanowania tłumaczeń (`i18n:scan`) oraz automatyczny test integralności.

Kluczowe założenia:
1. **Source of Truth**: Kod źródłowy (użycie funkcji `t()` i `__()`) jest źródłem prawdy dla kluczy.
2. **Normalizacja**: Klucze w panelu admina używają w kodzie prefixu `admin.`, ale w bazie są składowane w grupie `admin` bez prefixu.
3. **Automatyzacja**: Komenda `i18n:scan` automatycznie wciąga nowe klucze do DB.
4. **Weryfikacja**: Test `TranslationIntegrityTest` blokuje commity, jeśli w kodzie są klucze, których nie ma w seederach danych.

## Konsekwencje
### Pozytywne
- Brak "dziur" w tłumaczeniach UI.
- Szybszy development (brak konieczności ręcznego dodawania kluczy do DB).
- Spójność między środowiskami dzięki seederom opartym na plikach danych.

### Negatywne
- Konieczność pamiętania o aktualizacji seederów danych (`database/seeders/data/translations/`) przy dodawaniu nowych kluczy (wymuszane przez test).
