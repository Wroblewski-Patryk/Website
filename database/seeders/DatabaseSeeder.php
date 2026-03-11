<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Create Languages
        $languages = [
            ['code' => 'en', 'name' => 'English', 'is_default' => true, 'is_active' => true],
            ['code' => 'pl', 'name' => 'Polski', 'is_default' => false, 'is_active' => true],
            ['code' => 'de', 'name' => 'Deutsch', 'is_default' => false, 'is_active' => true],
            ['code' => 'fr', 'name' => 'Français', 'is_default' => false, 'is_active' => true],
        ];

        foreach ($languages as $lang) {
            Language::create($lang);
        }

        // 1. Create Admin User
        User::firstOrCreate(
        ['email' => 'admin@admin.com'],
        [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]
        );

        // 2. Create Default Home Page
        $homePage = Page::create(
        [
            'slug' => ['en' => 'home', 'pl' => 'home'],
            'title' => ['en' => 'Home', 'pl' => 'Strona Główna'],
            'status' => 'published',
            'content' => [
                [
                    'id' => 'block_' . Str::random(9),
                    'type' => 'hero',
                    'content' => [
                        'headline' => 'Welcome to our New Website',
                        'subheadline' => 'This page is powered by our pure Vue 3 Block Builder',
                    ],
                    'appearance' => [
                        'backgroundColor' => 'transparent',
                        'textColor' => 'inherit',
                        'paddingTop' => '4rem',
                        'paddingBottom' => '4rem',
                    ]
                ],
                [
                    'id' => 'block_' . Str::random(9),
                    'type' => 'text',
                    'content' => [
                        'text' => '<h2>Start building your amazing site</h2><p>Go to the admin panel to edit this page and add more blocks.</p>',
                    ],
                    'appearance' => [
                        'backgroundColor' => 'transparent',
                        'textColor' => 'inherit',
                        'paddingTop' => '2rem',
                        'paddingBottom' => '2rem',
                    ]
                ]
            ]
        ]
        );

        // 3. Create Default Settings
        $defaultSettings = [
            'site_name' => [
                'en' => 'Featherly CMS',
                'pl' => 'Featherly CMS PL',
                'de' => 'Featherly CMS DE',
                'fr' => 'Featherly CMS FR'
            ],
            'home_page_id' => $homePage->id,
            'blog_page_id' => null,
            'default_header_id' => null,
            'default_footer_id' => null,
            'theme_colors' => [
                'primary' => '#4f46e5',
                'secondary' => '#10b981',
                'accent' => '#f59e0b',
                'base_100' => '#ffffff',
                'base_200' => '#f3f4f6',
                'base_300' => '#e5e7eb',
                'neutral' => '#1f2937',
                'info' => '#3b82f6',
                'success' => '#22c55e',
                'warning' => '#eab308',
                'error' => '#ef4444'
            ],
            'theme_typography' => [
                'baseTextSize' => '16px',
                'baseLineHeight' => '1.5',
                'headingScale' => '1.25'
            ],
            'theme_radius' => [
                'none' => '0px',
                'sm' => '0.125rem',
                'md' => '0.375rem',
                'lg' => '0.5rem',
                'xl' => '0.75rem',
                '2xl' => '1rem',
                '3xl' => '1.5rem',
                'full' => '9999px',
                'box' => '1rem'
            ]
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value
            ]);
        }
    }
}
