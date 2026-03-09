# Projekt: System CMS z dodatkowymi modułami

## Stan obecny

Obecna witryna działa jako statyczny HTML z kilkoma elementami JS i prostym formularzem kontaktowym.  Zawartość jest zakodowana w pliku szablonu, co utrudnia modyfikacje.  Projekt ten ma zastąpić statyczną stronę kompletnym systemem CMS opartym na **Laravel 12** i **Vue 3**, wzbogaconym o moduły bloga, portfolio, wielojęzyczność, kreator formularzy oraz konfigurację globalną.

## Cele projektu

- **Zarządzanie stronami bez edycji kodu** – administrator może tworzyć, edytować i publikować strony za pomocą edytora blokowego opartego na JSON.
- **Budowa z bloków** – treści są konstruowane z różnorodnych bloków (tekst, media, układ, komponenty marketingowe, nawigacja itp.) z możliwością konfiguracji ich ustawień.
- **Animacje GSAP** – każdy blok może posiadać ustawienia animacji (triggery, preset, czas trwania, opóźnienie, ease), a na poziomie strony można tworzyć sekwencje (timeline).  Animacje są traktowane jako progresywne uzupełnienie treści, a strony muszą być czytelne bez JS.
- **Blog** – możliwość tworzenia i publikacji artykułów, wersjonowanie wpisów oraz wyświetlanie listy i szczegółów wpisu na stronie publicznej.
- **Portfolio projektów** – moduł do zarządzania projektami/realizacjami z pól typu tytuł, opis, zdjęcia i link, z obsługą sortowania.
- **Wielojęzyczność** – treści stron, wpisów, projektów i tłumaczeń interfejsu są przechowywane w kilku językach z możliwością przełączania między `pl` i `en`.
- **Kreator formularzy** – oprócz wbudowanego bloku formularza kontaktowego system pozwala tworzyć własne formularze (np. zapisy na newsletter) i zarządzać ich ustawieniami.
- **Konfiguracja globalna** – panel ustawień umożliwia definiowanie wartości globalnych (np. identyfikator strony startowej, domyślne szablony) dostępnych dla całej aplikacji.

## Kryteria sukcesu (MVP)

- Możliwość odtworzenia wszystkich kluczowych stron i podstron (w tym blog i portfolio) za pomocą edytora blokowego.
- Treści są w pełni tłumaczalne; użytkownik może przełączyć język i uzyskać przetłumaczoną wersję.
- Administrator ma dostęp do modułu bloga, projektów, formularzy, mediów, menu, szablonów, tłumaczeń i ustawień.
- Publiczne strony renderują się po stronie serwera i są zoptymalizowane pod SEO; animacje GSAP stanowią jedynie dekorację.
- Formularz kontaktowy działa i zabezpiecza przed spamem; inne formularze zapisują zgłoszenia w bazie.

## Non‑goals (MVP)

- Pełny klon zaawansowanych builderów (np. Elementor Pro) – nie wspieramy wizualnej edycji Figma‑like, skomplikowanych efektów 3D ani rynku zewnętrznych bloków.
- Multi‑tenant SaaS – system nie obsługuje wielu odizolowanych instancji klientów.
- Sklep internetowy – brak modułu e‑commerce, koszyka, płatności.
- Zaawansowana analityka – nie ma rozbudowanych paneli statystyk; proste informacje można dodać w przyszłości.

## Technologia

- **Backend:** Laravel 12, monolityczne API oraz SSR dla stron publicznych.
- **Frontend/Admin:** Vue 3 + Inertia.js + Tailwind CSS; panel admina jest aplikacją SPA, podczas gdy strony publiczne są renderowane serwerowo i wzbogacane o GSAP.
- **Baza danych:** MariaDB/MySQL; migracje tworzą tabele dla stron, wpisów, projektów, bloków (jako JSON), mediów, formularzy, tłumaczeń, języków, kontaktów i ustawień.
- **Pakiety pomocnicze:** Spatie Translatable (wielojęzyczność), Inertia, Pinia, Vuedraggable, DaisyUI.

## Ograniczenia i założenia

- Projekt realizowany jest przez jedną osobę, dlatego ważna jest prostota implementacji i łatwe utrzymanie kodu.
- Animacje powinny pozostawać dodatkiem; w trybie „prefer‑reduced‑motion” system redukuje lub wyłącza animacje.
- Strony publiczne muszą być w pełni dostępne bez obsługi JS; interaktywność jest warstwą dodatnią.
- Każdy nowy blok powinien być projektowany tak, aby jego dodanie nie wymagało zmian w schemacie bazy (obsługa poprzez JSON).  Blok powinien mieć schemat walidacji, renderer publiczny i komponent edycyjny.

---

## Kluczowe nowe założenia (2026-03)

Poniższe wymagania wynikają z potrzeby odtworzenia Twojej „legacy” strony (statyczny HTML+JS+PHP) w nowym CMS oraz z docelowego kierunku: **CMS jak WordPress (Gutenberg/Elementor), ale w Laravel+Vue**.

### 1) Tryb „Scene / Section” zamiast klasycznego scrolla

Twoja strona nie jest przewijana. Widok przełącza się pomiędzy pełnoekranowymi sekcjami („scenami”) przez:

- scroll wheel,
- strzałki klawiatury,
- zmianę hasha / ścieżki w URL (nawigacja stanem aplikacji).

Każda scena ma animację wejścia i wyjścia. Wyjście jest osobnym timeline albo odwróceniem (reversed) wejścia.

### 2) Animacje jako oś projektu (GSAP timeline per scena + globalne warstwy)

- Każda scena ma **enterTimeline** oraz **exitTimeline**.
- System musi posiadać **globalny kontroler przejść** (Transition Controller), który blokuje kolejne przejścia, gdy aktualna animacja jest aktywna (lock / guard).
- Oprócz elementów scen istnieją też **warstwy globalne** (np. tło, ramka, tytuły, dekoracje), które mogą animować się niezależnie od scen.

### 3) Warstwy, z-index i podgląd „3D” w edytorze

W edytorze musisz mieć możliwość:

- ustawiania `position` (relative/absolute/fixed/sticky),
- ustawiania `z-index` oraz **konceptualnego Z-depth** (np. `translateZ`) do celów podglądu,
- pracy na drzewie warstw (grupy, lock/hide, drag&drop),
- przełączenia edytora w tryb **podglądu głębi (pseudo-3D)**, aby widzieć układ elementów „w głąb”, a nie tylko „płasko”.

> Uwaga: chodzi o **narzędzie edycyjne** (podgląd/organizacja), nie o pełne 3D jak w silniku gier.

### 4) Timeline editor w panelu

W module edycji strony (oraz w innych modułach, gdzie używany jest konfigurator blokowy) potrzebujesz:

- edytora timeline (play/pause + scrubber),
- przypinania presetów animacji do bloków,
- definiowania timeline na scenie (oraz opcjonalnie na poziomie bloku),
- zapisu konfiguracji w formacie JSON, który runtime kompiluje do GSAP.

### 5) Legacy jako punkt odniesienia

Do projektu dołączone są pliki legacy (ZIP), z których należy wyciągnąć wzorce:

- przełączanie sekcji (wheel/keyboard),
- synchronizacja URL (hash/path),
- blokowanie przejść w trakcie animacji (np. `timeline.isActive()` / guard),
- enter/exit per sekcja oraz warstwy globalne.

Szczegółową mapę migracji opisuje plik: `docs/legacy-migration.md`.
