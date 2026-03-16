# Moduł: Layout Engine

## Cel modułu
System zarządzania układem i responsywnością, który zapewnia spójność wizualną między edytorem (Block Builder) a stroną publiczną (Renderer).

## Odpowiedzialność
- Definiowanie siatki (Grid System) i kontenerów.
- Obsługa ustawień responsywnych (Mobile/Desktop overrides).
- Zarządzanie marginesami, paddingami i wyrównaniem bloków.
- Dostarczanie klas narzędziowych (utilities) zgodnych z design systemem.

## Mechanika
Layout Engine opiera się na zestawie stałych (tokens) zdefiniowanych w `index.css` oraz logice wewnątrz bloków, która interpretuje obiekt `settings`.

Typowe ustawienia layoutu:
- `maxWidth`: Szerokość kontenera (np. Narrow, Wide, Full).
- `padding`: Odstępy wewnętrzne (zależne od skali radius/spacing).
- `alignment`: Wyrównanie treści (left, center, right, justify).

## Integracja z Motywem
Silnik korzysta z wartości ustawionych w Global Settings (np. `theme_radius`, `theme_colors`), co pozwala na dynamiczną zmianę wyglądu całej aplikacji bez modyfikacji poszczególnych bloków.
