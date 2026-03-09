# Modul bloga

## Stan obecny

- Blog dziala na modelu `Post`.
- Wpisy sa wielojezyczne (`title`, `slug`, `excerpt`, `featured_image`, pola SEO).
- Tresc wpisu jest budowana blokowo (`content` JSON).
- Rewizje sa zapisywane przed aktualizacja.

## Operacje admin

- Lista wpisow
- Tworzenie/edycja/usuwanie
- Statusy (`draft`, `published`, `planned`, `archived`)
- Pola SEO

## Publiczny widok

- Lista wpisow (strona blogowa)
- Szczegoly wpisu po slug

## Kategorie i tagi

- Na tym etapie brak osobnego modulu kategorii/tagow dla bloga.
- Rozszerzenie o kategorie/tagi jest planowane.