<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;

class TaxonomyTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            'taxonomy.management' => ['pl' => 'Zarządzanie Taksonomiami', 'en' => 'Taxonomy Management'],
            'taxonomy.title' => ['pl' => 'Tytuł', 'en' => 'Title'],
            'taxonomy.slug' => ['pl' => 'Slug', 'en' => 'Slug'],
            'taxonomy.order' => ['pl' => 'Kolejność', 'en' => 'Order'],
            'taxonomy.type_category' => ['pl' => 'Kategorie', 'en' => 'Categories'],
            'taxonomy.type_tag' => ['pl' => 'Tagi', 'en' => 'Tags'],
            'taxonomy.edit' => ['pl' => 'Edytuj Taksonomię', 'en' => 'Edit Taxonomy'],
            'taxonomy.add_new' => ['pl' => 'Dodaj Nową Taksonomię', 'en' => 'Add New Taxonomy'],
            'taxonomy.title_label' => ['pl' => 'Tytuł', 'en' => 'Title'],
            'taxonomy.slug_label' => ['pl' => 'Uproszczona nazwa (Slug)', 'en' => 'Slug'],
            'taxonomy.description_label' => ['pl' => 'Opis', 'en' => 'Description'],
            'taxonomy.order_label' => ['pl' => 'Kolejność (priorytet)', 'en' => 'Order'],
            'taxonomy.color_label' => ['pl' => 'Kolor', 'en' => 'Color'],
            'taxonomy.update_btn' => ['pl' => 'Aktualizuj', 'en' => 'Update'],
            'taxonomy.create_btn' => ['pl' => 'Utwórz', 'en' => 'Create'],
            'taxonomy.description_hint' => ['pl' => 'Organizuj treści za pomocą semantycznych grup.', 'en' => 'Organize content using semantic grouping.'],
            'taxonomy.created' => ['pl' => 'Taksonomia została utworzona.', 'en' => 'Taxonomy has been created.'],
            'taxonomy.updated' => ['pl' => 'Taksonomia została zaktualizowana.', 'en' => 'Taxonomy has been updated.'],
            'taxonomy.deleted' => ['pl' => 'Taksonomia została usunięta.', 'en' => 'Taxonomy has been deleted.'],
        ];

        foreach ($translations as $key => $values) {
            Translation::updateOrCreate(
                ['key' => 'admin.' . $key],
                ['text' => $values]
            );
        }
    }
}
