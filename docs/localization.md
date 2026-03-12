# Lokalizacja i tlumaczenia

## Aktualne zalozenia (2026-03-12)
- Aplikacja uzywa jednego aktywnego locale dla calego requestu.
- Nie rozdzielamy locale na osobne scope admin/public.
- Zrodlem dostepnych jezykow jest tabela `languages` (`is_active`, `is_default`).

## Regula wyboru jezyka
1. Jesli URL zawiera prefiks jezyka (`/{locale}/...`), to ten jezyk jest nadrzedny.
2. Jesli URL nie zawiera jezyka, aplikacja probuje autodetekcji z naglowka `Accept-Language`.
3. Jesli autodetekcja nie znajdzie dopasowania, aplikacja uzywa jezyka domyslnego.

## Przelaczanie jezyka
- Przelaczniki jezyka (admin i public) powinny operowac na tej samej liscie aktywnych jezykow z `languages`.
- Zmiana jezyka prowadzi do URL z odpowiednim prefiksem locale.

## Tlumaczenia tresci i UI
- Tresci modeli (`Page`, `Post`, `Project`) korzystaja z pol translatable (JSON).
- Tlumaczenia UI korzystaja z tabeli `translations` (`group`, `key`, `text`).
- Fallback tlumaczen jest oparty o aktualne locale i jezyk domyslny.