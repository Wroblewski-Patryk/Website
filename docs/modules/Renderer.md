# Moduł: Renderer

## Cel modułu
Transformacja surowych danych JSON (wygenerowanych przez Block Builder) na gotową warstwę prezentacji (HTML/Vue) widoczną dla użytkownika końcowego.

## Odpowiedzialność
- Dynamiczne rozwiązywanie komponentów na podstawie pola `type` bloku.
- Przekazywanie danych (`props`) i ustawień (`settings`) do konkretnych komponentów UI.
- Obsługa lokalizacji treści wewnątrz bloków.
- Integracja z systemem animacji w trybie runtime.

## Działanie
Renderer iteruje po tablicy bloków i dla każdego z nich:
1. Sprawdza czy typ bloku jest zarejestrowany.
2. Inicjalizuje komponent z odpowiednimi danymi.
3. Nakłada style systemowe (Layout Engine) zdefiniowane w `settings`.

## Kluczowe pliki
- `resources/js/features/renderer/BlockRenderer.vue`: Główny komponent odpowiedzialny za renderowanie listy bloków.
- `resources/js/features/renderer/blocks/`: Katalog z implementacjami wizualnymi poszczególnych bloków.
