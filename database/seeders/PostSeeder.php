<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Post::create([
            'title' => [
                'pl' => 'Przykładowy wpis na blogu',
                'en' => 'Example blog post'
            ],
            'slug' => [
                'pl' => 'przykladowy-wpis',
                'en' => 'example-post'
            ],
            'excerpt' => [
                'pl' => 'To jest krótki fragment przykładowego wpisu.',
                'en' => 'This is a short excerpt of the example post.'
            ],
            'status' => 'published',
            'published_at' => now(),
            'content' => [
                'pl' => [
                    [
                        'id' => 'block_' . \Illuminate\Support\Str::random(9),
                        'type' => 'text',
                        'content' => [
                            'text' => '<p>Cześć! To jest treść wpisu w języku polskim.</p>',
                        ],
                        'appearance' => [
                            'paddingTop' => '2rem',
                            'paddingBottom' => '2rem',
                        ]
                    ]
                ],
                'en' => [
                    [
                        'id' => 'block_' . \Illuminate\Support\Str::random(9),
                        'type' => 'text',
                        'content' => [
                            'text' => '<p>Hello! This is post content in English.</p>',
                        ],
                        'appearance' => [
                            'paddingTop' => '2rem',
                            'paddingBottom' => '2rem',
                        ]
                    ]
                ]
            ]
        ]);

        \App\Models\Post::create([
            'title' => [
                'pl' => 'Nowoczesna architektura stron',
                'en' => 'Modern website architecture'
            ],
            'slug' => [
                'pl' => 'nowoczesna-architektura',
                'en' => 'modern-architecture'
            ],
            'excerpt' => [
                'pl' => 'Dowiedz się więcej o nowoczesnym budowaniu stron.',
                'en' => 'Learn more about modern website building.'
            ],
            'status' => 'published',
            'published_at' => now(),
            'content' => [
                'pl' => [
                    [
                        'id' => 'block_' . \Illuminate\Support\Str::random(9),
                        'type' => 'hero',
                        'content' => [
                            'headline' => 'Buduj szybciej',
                            'subheadline' => 'Z naszym systemem blokowym.',
                        ],
                        'appearance' => [
                            'paddingTop' => '4rem',
                            'paddingBottom' => '4rem',
                        ]
                    ]
                ],
                'en' => [
                    [
                        'id' => 'block_' . \Illuminate\Support\Str::random(9),
                        'type' => 'hero',
                        'content' => [
                            'headline' => 'Build faster',
                            'subheadline' => 'With our block system.',
                        ],
                        'appearance' => [
                            'paddingTop' => '4rem',
                            'paddingBottom' => '4rem',
                        ]
                    ]
                ]
            ]
        ]);
    }
}
