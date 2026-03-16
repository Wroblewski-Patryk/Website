# Moduł: Block Builder

## Cel modułu
Wizualny edytor treści oparty na blokach, pozwalający na budowanie struktury stron, wpisów i projektów bez znajomości kodu.

## Odpowiedzialność
- Zarządzanie stanem edytora (Pinia store: `useBlockBuilderStore`).
- Obsługa interakcji użytkownika (drag-and-drop, zaznaczanie bloków, zmiana kolejności).
- Konfiguracja parametrów bloków (Sidebar).
- Zarządzanie historią zmian (Undo/Redo).
- Integracja z systemem animacji (GSAP Timeline).

## Architektura i UI
- **`BlockBuilderMain.vue`**: Główny kontener edytora.
- **`BlockBuilderHeader.vue`**: Pasek narzędziowy (skala, urządzenia, zapis).
- **`BlockBuilderSidebar.vue`**: Inspektor właściwości bloku i ustawienia globalne.
- **`BlockBuilderCanvas.vue`**: Obszar roboczy, w którym renderowane są bloki.

## Struktura danych bloku
Każdy blok w tablicy `content` posiada:
- `id`: Unikalny identyfikator (UUID).
- `type`: Typ bloku (np. `Hero`, `TextContent`, `Image`).
- `props`: Obiekt z danymi bloku (teksty, obrazy, linki).
- `settings`: Ustawienia wizualne (paddingi, tła, animacje).

## Rejestracja bloków
Nowe bloki są rejestrowane w `resources/js/features/admin/block-builder/registry.js`. Każdy wpis definiuje komponent edytora, komponent renderera oraz domyślne dane (`defaultData`).
