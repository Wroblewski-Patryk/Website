<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'key' => 'home',
                'title' => ['en' => 'Home', 'pl' => 'Strona Główna'],
                'slug' => ['en' => 'home', 'pl' => 'home'],
                'content' => [
                    'pl' => [
                        [
                            'id' => 'hero_1', 
                            'type' => 'hero', 
                            'content' => [
                                'headline' => 'Tworzymy Cyfrowe Doświadczenia', 
                                'subheadline' => 'Nowoczesne strony internetowe, które przyciągają uwagę i realizują cele Twojego biznesu.',
                                'primaryLabel' => 'Nasze Projekty'
                            ], 
                            'settings' => [
                                'style' => [
                                    'paddingTop' => '10rem', 
                                    'paddingBottom' => '10rem',
                                    'textAlign' => 'center'
                                ]
                            ]
                        ],
                        [
                            'id' => 'text_1',
                            'type' => 'heading',
                            'content' => [
                                'level' => 2,
                                'text' => 'Dlaczego my?',
                                'align' => 'center'
                            ],
                            'settings' => [
                                'style' => [
                                    'paddingTop' => '4rem',
                                    'marginBottom' => '2rem'
                                ]
                            ]
                        ],
                        [
                            'id' => 'text_2',
                            'type' => 'paragraph',
                            'content' => [
                                'text' => '<p class="text-center max-w-2xl mx-auto">Nasza pasja to technologia i design. Łączymy te dwa światy, aby dostarczać produkty najwyższej jakości.</p>'
                            ],
                            'settings' => [
                                'style' => [
                                    'paddingBottom' => '6rem'
                                ]
                            ]
                        ]
                    ],
                    'en' => [
                        [
                            'id' => 'hero_1', 
                            'type' => 'hero', 
                            'content' => [
                                'headline' => 'Crafting Digital Experiences', 
                                'subheadline' => 'Modern websites that capture attention and fulfill your business goals.',
                                'primaryLabel' => 'Our Projects'
                            ], 
                            'settings' => [
                                'style' => [
                                    'paddingTop' => '10rem', 
                                    'paddingBottom' => '10rem',
                                    'textAlign' => 'center'
                                ]
                            ]
                        ],
                        [
                            'id' => 'text_1',
                            'type' => 'heading',
                            'content' => [
                                'level' => 2,
                                'text' => 'Why Us?',
                                'align' => 'center'
                            ],
                            'settings' => [
                                'style' => [
                                    'paddingTop' => '4rem',
                                    'marginBottom' => '2rem'
                                ]
                            ]
                        ],
                        [
                            'id' => 'text_2',
                            'type' => 'paragraph',
                            'content' => [
                                'text' => '<p class="text-center max-w-2xl mx-auto">Our passion is technology and design. We bridge these worlds to deliver top-quality products.</p>'
                            ],
                            'settings' => [
                                'style' => [
                                    'paddingBottom' => '6rem'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'key' => 'blog',
                'title' => ['en' => 'Blog', 'pl' => 'Blog'],
                'slug' => ['en' => 'blog', 'pl' => 'blog'],
                'content' => [
                    'pl' => [
                        [
                            'id' => 'text_1', 
                            'type' => 'heading', 
                            'content' => ['text' => 'Nasz Blog', 'level' => 1, 'align' => 'center'], 
                            'settings' => ['style' => ['paddingTop' => '6rem', 'paddingBottom' => '2rem']]
                        ]
                    ],
                    'en' => [
                        [
                            'id' => 'text_1', 
                            'type' => 'heading', 
                            'content' => ['text' => 'Our Blog', 'level' => 1, 'align' => 'center'], 
                            'settings' => ['style' => ['paddingTop' => '6rem', 'paddingBottom' => '2rem']]
                        ]
                    ]
                ]
            ],
            [
                'key' => 'projects',
                'title' => ['en' => 'Projects', 'pl' => 'Projekty'],
                'slug' => ['en' => 'projects', 'pl' => 'projekty'],
                'content' => [
                    'pl' => [
                        [
                            'id' => 'text_1', 
                            'type' => 'heading', 
                            'content' => ['text' => 'Nasze Projekty', 'level' => 1, 'align' => 'center'], 
                            'settings' => ['style' => ['paddingTop' => '6rem', 'paddingBottom' => '2rem']]
                        ]
                    ],
                    'en' => [
                        [
                            'id' => 'text_1', 
                            'type' => 'heading', 
                            'content' => ['text' => 'Our Projects', 'level' => 1, 'align' => 'center'], 
                            'settings' => ['style' => ['paddingTop' => '6rem', 'paddingBottom' => '2rem']]
                        ]
                    ]
                ]
            ],
            [
                'key' => '404',
                'title' => ['en' => 'Page Not Found', 'pl' => 'Strona nie znaleziona'],
                'slug' => ['en' => '404', 'pl' => '404'],
                'content' => [
                    'pl' => [
                        ['id' => 'hero_1', 'type' => 'hero', 'content' => ['headline' => 'Błąd 404', 'subheadline' => 'Przepraszamy, ale ta strona nie istnieje.'], 'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']]]
                    ],
                    'en' => [
                        ['id' => 'hero_1', 'type' => 'hero', 'content' => ['headline' => '404 Error', 'subheadline' => 'Sorry, this page doesn\'t exist.'], 'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']]]
                    ]
                ]
            ],
            [
                'key' => 'maintenance',
                'title' => ['en' => 'Maintenance Mode', 'pl' => 'Przerwa techniczna'],
                'slug' => ['en' => 'maintenance', 'pl' => 'przerwa-techniczna'],
                'content' => [
                    'pl' => [
                        ['id' => 'hero_1', 'type' => 'hero', 'content' => ['headline' => 'Przerwa techniczna', 'subheadline' => 'Wracamy wkrótce!'], 'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']]]
                    ],
                    'en' => [
                        ['id' => 'hero_1', 'type' => 'hero', 'content' => ['headline' => 'Maintenance Mode', 'subheadline' => 'We\'ll be back soon!'], 'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']]]
                    ]
                ]
            ],
            [
                'key' => 'coming_soon',
                'title' => ['en' => 'Coming Soon', 'pl' => 'Już wkrótce'],
                'slug' => ['en' => 'coming-soon', 'pl' => 'juz-wkrotce'],
                'content' => [
                    'pl' => [
                        ['id' => 'hero_1', 'type' => 'hero', 'content' => ['headline' => 'Już wkrótce', 'subheadline' => 'Pracujemy nad czymś niesamowitym.'], 'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']]]
                    ],
                    'en' => [
                        ['id' => 'hero_1', 'type' => 'hero', 'content' => ['headline' => 'Coming Soon', 'subheadline' => 'We are working on something amazing.'], 'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']]]
                    ]
                ]
            ],
        ];

        foreach ($pages as $p) {
            \App\Models\Page::updateOrCreate(
                ['slug->pl' => $p['slug']['pl']],
                [
                    'title' => $p['title'],
                    'slug' => $p['slug'],
                    'content' => $p['content'],
                    'status' => 'published'
                ]
            );
        }
    }
}
