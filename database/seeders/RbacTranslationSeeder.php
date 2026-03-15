<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;

class RbacTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            [
                'group' => 'admin',
                'key' => 'menu.localization',
                'text' => ['pl' => 'Lokalizacja', 'en' => 'Localization']
            ],
            [
                'group' => 'admin',
                'key' => 'menu.users_management',
                'text' => ['pl' => 'Użytkownicy i Role', 'en' => 'Users & Roles']
            ],
            [
                'group' => 'admin',
                'key' => 'menu.roles',
                'text' => ['pl' => 'Role', 'en' => 'Roles']
            ],
            [
                'group' => 'admin',
                'key' => 'users.role',
                'text' => ['pl' => 'Rola', 'en' => 'Role']
            ],
        ];

        foreach ($translations as $data) {
            $translation = Translation::firstOrNew([
                'group' => $data['group'],
                'key' => $data['key']
            ]);
            
            foreach ($data['text'] as $locale => $value) {
                $translation->setTranslation('text', $locale, $value);
            }
            
            $translation->save();
        }
        
        // Clear cache
        \Illuminate\Support\Facades\Cache::forget('translations.pl');
        \Illuminate\Support\Facades\Cache::forget('translations.en');
    }
}
