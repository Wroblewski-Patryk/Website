<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;

class AdminUiTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            // Menu Groups
            'menu.content' => ['pl' => 'Treści', 'en' => 'Content'],
            'menu.pages' => ['pl' => 'Strony', 'en' => 'Pages'],
            'menu.posts' => ['pl' => 'Wpisy', 'en' => 'Posts'],
            'menu.categories' => ['pl' => 'Kategorie', 'en' => 'Categories'],
            'menu.projects' => ['pl' => 'Projekty', 'en' => 'Projects'],
            'menu.clients' => ['pl' => 'Klienci', 'en' => 'Clients'],
            'menu.forms' => ['pl' => 'Formularze', 'en' => 'Forms'],
            'menu.library' => ['pl' => 'Biblioteka', 'en' => 'Library'],
            'menu.media' => ['pl' => 'Media', 'en' => 'Media'],
            'menu.templates' => ['pl' => 'Szablony', 'en' => 'Templates'],
            'menu.design' => ['pl' => 'Wygląd', 'en' => 'Design'],
            'menu.theme' => ['pl' => 'Motyw', 'en' => 'Theme'],
            'menu.colors' => ['pl' => 'Kolory', 'en' => 'Colors'],
            'menu.fonts' => ['pl' => 'Fonty', 'en' => 'Fonts'],
            'menu.typography' => ['pl' => 'Typografia', 'en' => 'Typography'],
            'menu.metrics' => ['pl' => 'Wymiary', 'en' => 'Sizes / Metrics'],
            'menu.effects' => ['pl' => 'Cienie / Efekty', 'en' => 'Shadows / Effects'],
            'menu.blocks' => ['pl' => 'Bloki', 'en' => 'Blocks'],
            'menu.system' => ['pl' => 'System', 'en' => 'System Settings'],
            'menu.translations' => ['pl' => 'Tłumaczenia', 'en' => 'Translations'],
            'menu.languages' => ['pl' => 'Języki', 'en' => 'Languages'],
            'menu.users' => ['pl' => 'Użytkownicy', 'en' => 'Users'],
            'menu.settings' => ['pl' => 'Ustawienia', 'en' => 'Settings'],
            
            // Navigation Elements
            'nav.language' => ['pl' => 'Język', 'en' => 'Language'],
            'nav.my_profile' => ['pl' => 'Mój Profil', 'en' => 'My Profile'],
            'nav.account_settings' => ['pl' => 'Ustawienia konta', 'en' => 'Account Settings'],
            'nav.support' => ['pl' => 'Pomoc', 'en' => 'Support'],
            'nav.logout' => ['pl' => 'Wyloguj się', 'en' => 'Logout'],
            
            // Theme Picker
            'theme.select_theme' => ['pl' => 'Wybierz motyw', 'en' => 'Choose Theme'],
            
            // SEO
            'seo.admin_panel' => ['pl' => 'Panel Administracyjny', 'en' => 'Admin Panel'],

            // Dashboard
            'dashboard.title' => ['pl' => 'Pulpit', 'en' => 'Dashboard'],
            'dashboard.welcome_alert' => ['pl' => 'Witaj w panelu administracyjnym Featherly. Uwierzytelnienie przebiegło pomyślnie.', 'en' => 'Welcome to Featherly Administrator Panel. You have successfully authenticated.'],
            'dashboard.pages' => ['pl' => 'Strony', 'en' => 'Pages'],
            'dashboard.pages_desc' => ['pl' => 'Zarządzaj stronami swojej witryny i korzystaj z wizualnego edytora bloków.', 'en' => 'Manage your website pages and utilize the Visual Block Builder.'],
            'dashboard.manage_pages' => ['pl' => 'Zarządzaj Stronami', 'en' => 'Manage Pages'],
            'dashboard.posts' => ['pl' => 'Wpisy na Blogu', 'en' => 'Blog Posts'],
            'dashboard.posts_desc' => ['pl' => 'Twórz i publikuj artykuły na blogu z pełnymi ustawieniami SEO.', 'en' => 'Write and publish blog articles with full SEO settings.'],
            'dashboard.manage_posts' => ['pl' => 'Zarządzaj Wpisami', 'en' => 'Manage Posts'],
            
            // Settings Module
            'settings.global_settings' => ['pl' => 'Ustawienia Globalne', 'en' => 'Global Settings'],
            'settings.desc' => ['pl' => 'Zarządzaj podstawowym zachowaniem witryny i konfiguracjami routingu.', 'en' => 'Manage core website behavior and routing configurations.'],
            'settings.save_changes' => ['pl' => 'Zapisz Zmiany', 'en' => 'Save Changes'],
            'settings.routing_nav' => ['pl' => 'Routing i Nawigacja', 'en' => 'Routing & Navigation'],
            'settings.home_page' => ['pl' => 'Strona Główna', 'en' => 'Home Page'],
            'settings.home_page_desc' => ['pl' => 'Zachowanie głównego adresu URL (/).', 'en' => 'Root URL (/) behavior.'],
            'settings.blog_index' => ['pl' => 'Indeks Bloga', 'en' => 'Blog Index'],
            'settings.blog_index_desc' => ['pl' => 'Metadane listy wpisów blogowych.', 'en' => 'Blog listing meta data.'],
            'settings.projects_page' => ['pl' => 'Strona Projektów', 'en' => 'Projects Page'],
            'settings.projects_page_desc' => ['pl' => 'Źródło listy portfolio.', 'en' => 'Portfolio listing source.'],
            'settings.system_behavior' => ['pl' => 'Zachowanie Systemu i Konserwacja', 'en' => 'System Behavior & Maintenance'],
            'settings.system_behavior_desc' => ['pl' => 'Konfiguruj globalne przekierowania, stany i obsługę błędów.', 'en' => 'Configure global redirects, states, and error handling.'],
            'settings.maintenance_mode' => ['pl' => 'Tryb Konserwacji', 'en' => 'Maintenance Mode'],
            'settings.maintenance_page' => ['pl' => 'Strona Konserwacji', 'en' => 'Maintenance Page'],
            'settings.maintenance_page_desc' => ['pl' => 'Przekieruj tutaj, gdy tryb jest włączony.', 'en' => 'Redirect here when mode is ON.'],
            'settings.coming_soon' => ['pl' => 'Wkrótce (Coming Soon)', 'en' => 'Coming Soon'],
            'settings.coming_soon_desc' => ['pl' => 'Dla stron ze statusem "Planowane".', 'en' => 'For pages with "Planned" status.'],
            'settings.custom_404' => ['pl' => 'Własne 404', 'en' => 'Custom 404'],
            'settings.custom_404_desc' => ['pl' => 'Globalne miejsce docelowe błędów.', 'en' => 'Global error destination.'],
            'settings.seo_indexing' => ['pl' => 'SEO i Indeksowanie', 'en' => 'SEO & Indexing'],
            'settings.site_name' => ['pl' => 'Nazwa Witryny', 'en' => 'Site Name'],
            'settings.separator' => ['pl' => 'Separator', 'en' => 'Separator'],
            'settings.title_order' => ['pl' => 'Kolejność Tytułu', 'en' => 'Title Order'],
            'settings.include_home_title' => ['pl' => 'Dołącz Tytuł Główny', 'en' => 'Include Home Title'],
            'settings.meta_desc' => ['pl' => 'Domyślny Meta Opis', 'en' => 'Default Meta Description'],
            'settings.og_image' => ['pl' => 'Domyślny Obraz OG', 'en' => 'Default OG Image'],
            'settings.admin_noindex' => ['pl' => 'Admin No-Index', 'en' => 'Admin No-Index'],
            'settings.robots_disallow' => ['pl' => 'Robots Disallow Admin', 'en' => 'Robots Disallow Admin'],
            'settings.sitemap_enabled' => ['pl' => 'Mapa Strony Włączona', 'en' => 'Sitemap Enabled'],
            'settings.sitemap_cache' => ['pl' => 'Cache Mapy Strony (min)', 'en' => 'Sitemap Cache (min)'],
            'settings.none_404' => ['pl' => 'Brak (Zwraca 404)', 'en' => 'None (Returns 404)'],
            'settings.none_static' => ['pl' => 'Brak (Układ Statyczny)', 'en' => 'None (Static Layout)'],
            'settings.none_projects' => ['pl' => 'Brak (Projekty Statyczne)', 'en' => 'None (Static Projects)'],
            'settings.default_theme_page' => ['pl' => 'Domyślna strona motywu...', 'en' => 'Default theme page...'],
            'settings.show_regular_404' => ['pl' => 'Pokaż zwykłe 404...', 'en' => 'Show regular 404...'],
            'settings.standard_404' => ['pl' => 'Standardowe 404...', 'en' => 'Standard 404...'],
            'settings.select_image' => ['pl' => 'Wybierz Obraz', 'en' => 'Select Image'],
            'settings.remove' => ['pl' => 'Usuń', 'en' => 'Remove'],

            // Common Actions
            'common.actions' => ['pl' => 'Akcje', 'en' => 'Actions'],
            'common.edit' => ['pl' => 'Edytuj', 'en' => 'Edit'],
            'common.delete' => ['pl' => 'Usuń', 'en' => 'Delete'],
            'common.cancel' => ['pl' => 'Anuluj', 'en' => 'Cancel'],
            'common.save' => ['pl' => 'Zapisz zmiany', 'en' => 'Save Changes'],
            'common.active' => ['pl' => 'Aktywny', 'en' => 'Active'],
            'common.inactive' => ['pl' => 'Nieaktywny', 'en' => 'Inactive'],
            'common.create' => ['pl' => 'Utwórz', 'en' => 'Create'],
            'common.search_placeholder' => ['pl' => 'Szukaj...', 'en' => 'Search...'],
            'common.layout' => ['pl' => 'Układ', 'en' => 'Layout'],
            'common.toggle_columns' => ['pl' => 'Przełącz kolumny', 'en' => 'Toggle Columns'],
            'common.no_records' => ['pl' => 'Nie znaleziono pasujących rekordów', 'en' => 'No matching records found'],
            'common.showing' => ['pl' => 'Wyświetlanie', 'en' => 'Showing'],
            'common.to' => ['pl' => 'do', 'en' => 'to'],
            'common.of' => ['pl' => 'z', 'en' => 'of'],
            'common.entries' => ['pl' => 'wpisów', 'en' => 'entries'],
            'common.are_you_sure' => ['pl' => 'Czy na pewno?', 'en' => 'Are you sure?'],
            'common.delete_warning' => ['pl' => 'Tej operacji nie można cofnąć. Wszystkie dane powiązane z tym rekordem zostaną trwale usunięte.', 'en' => 'This action cannot be undone. All data associated with this record will be permanently removed.'],
            'common.confirm_delete' => ['pl' => 'Tak, usuń trwale', 'en' => 'Yes, Delete Permanently'],

            // Translations Module
            'translations.description' => ['pl' => 'Zarządzaj ciągami znaków w wielu językach dla interfejsu strony.', 'en' => 'Manage multi-language strings for your website UI.'],
            'translations.create' => ['pl' => 'Dodaj klucz', 'en' => 'Create Key'],
            'translations.group' => ['pl' => 'Grupa', 'en' => 'Group'],
            'translations.key' => ['pl' => 'Klucz', 'en' => 'Key'],
            'translations.add_new' => ['pl' => 'Dodaj nowy klucz tłumaczenia', 'en' => 'Add New Translation Key'],
            'translations.key_placeholder' => ['pl' => 'np. moj_klucz', 'en' => 'e.g. my_key'],
            'translations.value' => ['pl' => 'Wartość', 'en' => 'Value'],
            'translations.create_btn' => ['pl' => 'Utwórz tłumaczenie', 'en' => 'Create Translation'],
            'translations.edit_title' => ['pl' => 'Edytuj tłumaczenie', 'en' => 'Edit Translation'],

            // Languages Module
            'languages.management' => ['pl' => 'Zarządzanie językami', 'en' => 'Language Management'],
            'languages.description' => ['pl' => 'Zarządzaj dostępnymi lokalizacjami i internacjonalizacją strony.', 'en' => 'Manage available locales and site internationalization.'],
            'languages.name' => ['pl' => 'Nazwa', 'en' => 'Name'],
            'languages.code' => ['pl' => 'Kod', 'en' => 'Code'],
            'languages.status' => ['pl' => 'Status', 'en' => 'Status'],
            'languages.default' => ['pl' => 'Domyślny', 'en' => 'Default'],
            'languages.edit' => ['pl' => 'Edytuj język', 'en' => 'Edit Language'],
            'languages.add_new' => ['pl' => 'Dodaj nowy język', 'en' => 'Add New Language'],
            'languages.code_hint' => ['pl' => 'Kod języka (np. en, pl)', 'en' => 'Language Code (e.g. en, pl)'],
            'languages.name_hint' => ['pl' => 'Nazwa wyświetlana (np. Polski)', 'en' => 'Display Name (e.g. Polish)'],
            'languages.set_default' => ['pl' => 'Ustaw jako domyślny język', 'en' => 'Set as Default Language'],
            'languages.is_active' => ['pl' => 'Język jest aktywny', 'en' => 'Language is Active'],
            'languages.update_btn' => ['pl' => 'Aktualizuj język', 'en' => 'Update Language'],
            'languages.create_btn' => ['pl' => 'Utwórz język', 'en' => 'Create Language'],
        ];

        foreach ($translations as $key => $values) {
            Translation::updateOrCreate(
                ['group' => 'admin', 'key' => $key],
                ['text' => $values]
            );
        }
    }
}
