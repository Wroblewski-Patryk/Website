<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;

class AdminUiTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translationFiles = [
            'menu.php',
            'theme.php',
            'builder.php',
            'common.php',
            'pages.php',
            'posts.php',
            'projects.php',
            'clients.php',
            'media.php',
            'system.php',
            'forms.php',
            'composed_blocks.php',
            'forms_templates.php',
            'dashboard.php',
            'users.php',
            'roles.php',
            'login.php',
            'taxonomy.php',
            'templates.php',
            'seo.php',
            'settings.php',
        ];

        foreach ($translationFiles as $file) {
            $filePath = database_path('seeders/data/translations/' . $file);
            
            if (file_exists($filePath)) {
                $translations = require $filePath;
                
                foreach ($translations as $key => $values) {
                    // Normalize admin.* keys for the admin group:
                    // we want key="users.title" for group="admin" 
                    // even if the file has "admin.users.title"
                    $normalizedKey = str_starts_with($key, 'admin.') ? substr($key, 6) : $key;

                    Translation::updateOrCreate(
                        ['group' => 'admin', 'key' => $normalizedKey],
                        ['text' => $values]
                    );
                }
            }
        }
    }
}
