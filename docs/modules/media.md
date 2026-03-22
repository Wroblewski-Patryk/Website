# Media Module

Cel: Zarządzanie plikami multimedialnymi wykorzystywanymi na stronach, w postach, realizacjach i interfejsie użytkownika.

## Odpowiedzialność
- Przechowywanie, upload i udostępnianie plików w systemie (głównie zdjęcia, dokumenty, wideo).
- Serwowanie widoku biblioteki mediów w Panelu Administracyjnym.
- Zarządzanie logiczną strukturą (opcjonalne wirtualne lub realne foldery dla użytkownika, jednakże domyślnie płaska struktura plików na głównym poziomie `storage/app/public/media`).
- Integrowanie z komponentami Vue (Vue PreviewModal, GridItem, ListItem) dla gładkiego Visual Flow.

## Architektura i Założenia Seeding
Zgodnie z wymaganiami, struktura składowania plików mediów została spłaszczona. 

1. **Upload Użytkownika:**
   Wszystkie zrzucane pliki z poziomu admina wpadają bezpośrednio do głównego katalogu `/storage/app/public/media/` bez głębokiego zagnieżdżenia, co ułatwia podgląd od razu po odświeżeniu.
2. **MediaSeeder:**
   Seeder skanuje bezwzględnie zawartość folderu `storage/app/public/media/` przywracając plikom ich obiekty `Media` w bazie danych. **MediaSeeder nie generuje już struktury 'Seeded Content'.** Wszystkie wgrane pliki demonstracyjne (seeded) posiadają `folder_id = null`, lądując na pierwszej stronie interfejsu (Flat Level Directory) – mieszając się naturalnie z plikami ręcznie wgranymi przez użytkownika.
3. **Frontend components (Vue):**
   Renderowane adresy URL bazują bezpośrednio na dynamicznej własności `item.url` z back-endu, ignorując stary system ręcznego przedrostka `'/storage/' + item.path`.
