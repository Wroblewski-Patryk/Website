# Content Architecture (Phase L)

## Cel
Ujednolicenie struktury i logiki biznesowej dla głównych typów treści w systemie (`Page`, `Post`, `Project`).

## Odpowiedzialność
- Dostarczanie wspólnych cech (tłumaczenia, taksonomie, rewizje, SEO).
- Centralne zarządzanie logiką CRUD w panelu administracyjnym.
- Umożliwienie łatwego dodawania nowych typów treści w przyszłości.

## Kluczowe Komponenty

### 1. Modele (`HasContentFeatures` Trait)
Wszystkie modele treści muszą implementować ten trait, który zapewnia:
- Relację `taxonomies()` (MorphToMany).
- Relację `revisions()` (MorphMany).
- Scope `published()`.
- Metodę pomocniczą `hasSeoFields()`.

### 2. Kontrolery (`BaseAdminContentController`)
Abstrakcyjny kontroler zawierający:
- `getBaseIndexQuery()`: Wspólna logika wyszukiwania (po tytule/slugu) i sortowania (z uwzględnieniem JSON logic).
- `getSharedProps()`: Przygotowanie danych dla edytora (szablony, dostępne taksonomie).
- `syncTaxonomies()`: Helper do synchronizacji relacji wiele-do-wielu.
- `getBaseValidationRules()`: Standardowy zestaw reguł (tytuł, slug, SEO, status).

## Powiązania
- **Taxonomy**: Każdy model treści może być przypisany do kategorii i tagów.
- **Block Builder**: Wykorzystuje te modele do zapisu i odczytu ustrukturowanej treści.
- **Dynamic Routing**: `PageController` wykorzystuje taksonomie do filtrowania treści na froncie.
