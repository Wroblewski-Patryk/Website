<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;

class AdminUiTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            // Menu Groups (Sidebar)
            'menu.content' => ['pl' => 'Zawartość', 'en' => 'Content'],
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
            'menu.sizes' => ['pl' => 'Wymiary', 'en' => 'Sizes & Metrics'],
            'menu.effects' => ['pl' => 'Efekty', 'en' => 'Shadows & Effects'],
            'menu.blocks' => ['pl' => 'Bloki', 'en' => 'Blocks'],
            'menu.system' => ['pl' => 'System', 'en' => 'System Settings'],
            'menu.translations' => ['pl' => 'Tłumaczenia', 'en' => 'Translations'],
            'menu.languages' => ['pl' => 'Języki', 'en' => 'Languages'],
            'menu.users' => ['pl' => 'Użytkownicy', 'en' => 'Users'],
            'menu.settings' => ['pl' => 'Ustawienia', 'en' => 'Settings'],
            
            // Theme Module
            'theme.title' => ['pl' => 'Konfigurator Motywu', 'en' => 'Theme Configurator'],
            'theme.desc' => ['pl' => 'Dostosuj globalne kolory, typografię i parametry wizualne witryny.', 'en' => 'Fine-tune global colors, typography, and visual parameters of the website.'],
            'theme.save_btn' => ['pl' => 'Zapisz', 'en' => 'Save'],
            'theme.saved' => ['pl' => 'Zapisano', 'en' => 'Saved'],
            'theme.colors_title' => ['pl' => 'Kolory DaisyUI', 'en' => 'DaisyUI Colors'],
            'theme.colors_desc' => ['pl' => 'Zarządzaj główną paletą kolorów DaisyUI używaną we wszystkich blokach.', 'en' => 'Manage the main DaisyUI color palette used across all blocks.'],
            'theme.fonts_title' => ['pl' => 'Konfiguracja Fontów Google', 'en' => 'Google Fonts Configuration'],
            'theme.fonts_desc' => ['pl' => 'Wybierz fonty Google, aby wypełnić 3 główne stosy fontów Tailwind.', 'en' => 'Select Google Fonts to populate the 3 core Tailwind font stacks.'],
            'theme.sizes_title' => ['pl' => 'Wymiary i Miary', 'en' => 'Sizes & Metrics'],
            'theme.sizes_desc' => ['pl' => 'Skonfiguruj zaokrąglenia krawędzi dla elementów UI oraz maksymalną szerokość kontenera witryny.', 'en' => 'Configure border radius for UI elements and the maximum width for the website container.'],
            'theme.typography_title' => ['pl' => 'Metryki Typografii', 'en' => 'Typography Metrics'],
            'theme.typography_desc' => ['pl' => 'Precyzyjnie dostosuj wagi typografii, wysokości linii i odstępy między literami.', 'en' => 'Fine-tune typography weights, line heights, and letter spacing.'],
            'theme.effects_title' => ['pl' => 'Efekty Wizualne', 'en' => 'Visual Effects'],
            'theme.effects_desc' => ['pl' => 'Dostosuj efekty wizualne, takie jak cienie i rozmycia.', 'en' => 'Fine-tune visual effects like shadows and blurs.'],
            'theme.live_preview' => ['pl' => 'Podgląd na żywo', 'en' => 'Live Preview'],
            'theme.fallback_stacks' => ['pl' => 'Stosy zapasowe Tailwind', 'en' => 'Tailwind Fallback Stacks'],
            'theme.spacing_base' => ['pl' => 'Podstawowa skala odstępów', 'en' => 'Base Spacing Scale'],
            'theme.spacing_desc' => ['pl' => 'Mnożnik dla wszystkich marginesów i dopełnień.', 'en' => 'Multiplier for all padding/margin.'],
            'theme.global_shapes' => ['pl' => 'Globalne kształty i układ', 'en' => 'Global Shapes & Layout'],
            'theme.box_radius' => ['pl' => 'Zaokrąglenie pudełek (Box)', 'en' => 'Box Radius'],
            'theme.box_radius_desc' => ['pl' => 'Dotyczy kart, okien modalnych i dużych kontenerów.', 'en' => 'Applies to Cards, Modals, and large containers.'],
            'theme.container_width' => ['pl' => 'Maks. szerokość kontenera', 'en' => 'Container Max Width'],
            'theme.container_width_desc' => ['pl' => 'Maksymalna szerokość dla sekcji pudełkowych.', 'en' => 'The maximum width for boxed sections.'],
            'theme.btn_radius' => ['pl' => 'Zaokrąglenie przycisków', 'en' => 'Button Radius'],
            'theme.btn_radius_desc' => ['pl' => 'Dotyczy wszystkich standardowych przycisków.', 'en' => 'Applies to all standard buttons.'],
            'theme.badge_radius' => ['pl' => 'Zaokrąglenie odznak (Badge)', 'en' => 'Badge Radius'],
            'theme.badge_radius_desc' => ['pl' => 'Dotyczy pigułek i małych odznak.', 'en' => 'Applies to pills and small badges.'],
            'theme.font_sizes' => ['pl' => 'Rozmiary fontów', 'en' => 'Font Sizes'],
            'theme.font_weights' => ['pl' => 'Wagi fontów', 'en' => 'Font Weights'],
            'theme.line_heights' => ['pl' => 'Wysokości linii', 'en' => 'Line Heights'],
            'theme.letter_spacing' => ['pl' => 'Odstępy między literami', 'en' => 'Letter Spacing'],
            'theme.shadows' => ['pl' => 'Cienie', 'en' => 'Shadows'],
            'theme.blurs' => ['pl' => 'Rozmycia', 'en' => 'Blurs'],
            'theme.preview_box' => ['pl' => 'Pudełko', 'en' => 'Box'],
            'theme.preview_cards' => ['pl' => 'Karty i Modale', 'en' => 'Cards & Modals'],
            'theme.preview_primary_btn' => ['pl' => 'Przycisk Główny', 'en' => 'Primary Button'],
            'theme.preview_badge' => ['pl' => 'Odznaka Powiadomienia', 'en' => 'Notification Badge'],
            'theme.font_sans_title' => ['pl' => 'Font bezszeryfowy (Sans)', 'en' => 'Sans-Serif Font'],
            'theme.font_sans_desc' => ['pl' => 'Używany do głównej treści i tekstu interfejsu.', 'en' => 'Used for main body content and UI text.'],
            'theme.font_serif_title' => ['pl' => 'Font szeryfowy (Serif)', 'en' => 'Serif Font'],
            'theme.font_serif_desc' => ['pl' => 'Używany do nagłówków i bardziej ozdobnej typografii.', 'en' => 'Used for headings and more decorative typography.'],
            'theme.font_mono_title' => ['pl' => 'Font o stałej szerokości (Mono)', 'en' => 'Monospace Font'],
            'theme.font_mono_desc' => ['pl' => 'Używany do fragmentów kodu i danych technicznych.', 'en' => 'Used for code snippets and technical data.'],
            'theme.update_success' => ['pl' => 'Konfiguracja motywu została pomyślnie zaktualizowana.', 'en' => 'Theme configuration updated successfully.'],

            // Blocks Module
            'blocks.title' => ['pl' => 'Bloki', 'en' => 'Blocks'],
            'blocks.desc' => ['pl' => 'Zarządzaj globalnymi domyślnymi ustawieniami i strukturami bloków.', 'en' => 'Manage global block defaults and structures.'],
            'blocks.create' => ['pl' => 'Utwórz Blok', 'en' => 'Create Block'],
            'blocks.empty_state' => ['pl' => 'Nie skonfigurowano jeszcze domyślnych ustawień bloków. Wybierz blok, aby rozpocząć edycję jego domyślnych właściwości strukturalnych.', 'en' => 'No block defaults configured yet. Select a block to begin editing its default structural properties.'],

            // Common System Labels (ResourceTable etc.)
            'common.search_placeholder' => ['pl' => 'Szukaj...', 'en' => 'Search...'],
            'common.layout' => ['pl' => 'Układ', 'en' => 'Layout'],
            'common.toggle_columns' => ['pl' => 'Pokaż/ukryj kolumny', 'en' => 'Toggle Columns'],
            'common.create' => ['pl' => 'Stwórz', 'en' => 'Create'],
            'common.no_records' => ['pl' => 'Nie znaleziono pasujących rekordów', 'en' => 'No matching records found'],
            'common.showing' => ['pl' => 'Pokazuje', 'en' => 'Showing'],
            'common.to' => ['pl' => 'do', 'en' => 'to'],
            'common.of' => ['pl' => 'z', 'en' => 'of'],
            'common.entries' => ['pl' => 'wyników', 'en' => 'entries'],
            'common.are_you_sure' => ['pl' => 'Czy jesteś pewien?', 'en' => 'Are you sure?'],
            'common.delete_warning' => ['pl' => 'Tej operacji nie można cofnąć. Wszystkie powiązane dane zostaną trwale usunięte.', 'en' => 'This action cannot be undone. All data associated with this record will be permanently removed.'],
            'common.confirm_delete' => ['pl' => 'Tak, usuń permanentnie', 'en' => 'Yes, Delete Permanently'],
            'common.cancel' => ['pl' => 'Anuluj', 'en' => 'Cancel'],
            'common.actions' => ['pl' => 'Akcje', 'en' => 'Actions'],
            'dashboard.title' => ['pl' => 'Pulpit', 'en' => 'Dashboard'],
            'seo.admin_panel' => ['pl' => 'Panel Administratora', 'en' => 'Admin Panel'],
            'nav.my_profile' => ['pl' => 'Mój Profil', 'en' => 'My Profile'],
            'nav.account_settings' => ['pl' => 'Ustawienia Konta', 'en' => 'Account Settings'],
            'nav.support' => ['pl' => 'Wsparcie', 'en' => 'Support'],
            'nav.logout' => ['pl' => 'Wyloguj', 'en' => 'Logout'],
            'nav.language' => ['pl' => 'Język', 'en' => 'Language'],

            // Users Module
            'users.title' => ['pl' => 'Użytkownicy', 'en' => 'Users'],
            'users.desc' => ['pl' => 'Zarządzaj kontami użytkowników z dostępem do panelu.', 'en' => 'Manage user accounts with access to the panel.'],
            'users.full_name' => ['pl' => 'Imię i Nazwisko', 'en' => 'Full Name'],
            'users.email_address' => ['pl' => 'Adres E-mail', 'en' => 'Email Address'],
            'users.created_at' => ['pl' => 'Data utworzenia', 'en' => 'Created At'],
            'users.delete_confirm' => ['pl' => 'Czy napewno chcesz usunąć tego użytkownika?', 'en' => 'Are you sure you want to delete this user?'],
        ];

        foreach ($translations as $key => $values) {
            Translation::updateOrCreate(
                ['group' => 'admin', 'key' => $key],
                ['text' => $values]
            );
        }
    }
}
