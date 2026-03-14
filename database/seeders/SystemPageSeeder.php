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
                            'id' => 'section_1', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'flexConfig' => ['gap' => '4', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'col'], 'layoutType' => 'default'],
                            'children' => [
                                ['id' => 'h1_1', 'type' => 'heading', 'content' => ['text' => 'Tworzymy Cyfrowe Doświadczenia', 'level' => 1, 'align' => 'center'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'p_1', 'type' => 'paragraph', 'content' => ['text' => '<p class="text-center max-w-2xl mx-auto">Nowoczesne strony internetowe, które przyciągają uwagę i realizują cele Twojego biznesu.</p>'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'btn_1', 'type' => 'button', 'content' => ['label' => 'Nasze Projekty', 'url' => '/projekty', 'align' => 'center', 'style' => 'primary'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                            ],
                            'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']],
                            'parent_id' => null,
                        ],
                        [
                            'id' => 'text_1', 'type' => 'heading',
                            'content' => ['level' => 2, 'text' => 'Dlaczego my?', 'align' => 'center'],
                            'settings' => ['style' => ['paddingTop' => '4rem', 'marginBottom' => '2rem']]
                        ],
                        [
                            'id' => 'text_2', 'type' => 'paragraph',
                            'content' => ['text' => '<p class="text-center max-w-2xl mx-auto">Nasza pasja to technologia i design. Łączymy te dwa światy, aby dostarczać produkty najwyższej jakości.</p>'],
                            'settings' => ['style' => ['paddingBottom' => '6rem']]
                        ]
                    ],
                    'en' => [
                        [
                            'id' => 'section_1', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'flexConfig' => ['gap' => '4', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'col'], 'layoutType' => 'default'],
                            'children' => [
                                ['id' => 'h1_1', 'type' => 'heading', 'content' => ['text' => 'Crafting Digital Experiences', 'level' => 1, 'align' => 'center'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'p_1', 'type' => 'paragraph', 'content' => ['text' => '<p class="text-center max-w-2xl mx-auto">Modern websites that capture attention and fulfill your business goals.</p>'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'btn_1', 'type' => 'button', 'content' => ['label' => 'Our Projects', 'url' => '/projects', 'align' => 'center', 'style' => 'primary'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                            ],
                            'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']],
                            'parent_id' => null,
                        ],
                        [
                            'id' => 'text_1', 'type' => 'heading',
                            'content' => ['level' => 2, 'text' => 'Why Us?', 'align' => 'center'],
                            'settings' => ['style' => ['paddingTop' => '4rem', 'marginBottom' => '2rem']]
                        ],
                        [
                            'id' => 'text_2', 'type' => 'paragraph',
                            'content' => ['text' => '<p class="text-center max-w-2xl mx-auto">Our passion is technology and design. We bridge these worlds to deliver top-quality products.</p>'],
                            'settings' => ['style' => ['paddingBottom' => '6rem']]
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
                        ['id' => 'text_1', 'type' => 'heading', 'content' => ['text' => 'Nasz Blog', 'level' => 1, 'align' => 'center'], 'settings' => ['style' => ['paddingTop' => '6rem', 'paddingBottom' => '2rem']]]
                    ],
                    'en' => [
                        ['id' => 'text_1', 'type' => 'heading', 'content' => ['text' => 'Our Blog', 'level' => 1, 'align' => 'center'], 'settings' => ['style' => ['paddingTop' => '6rem', 'paddingBottom' => '2rem']]]
                    ]
                ]
            ],
            [
                'key' => 'projects',
                'title' => ['en' => 'Projects', 'pl' => 'Projekty'],
                'slug' => ['en' => 'projects', 'pl' => 'projekty'],
                'content' => [
                    'pl' => [
                        ['id' => 'text_1', 'type' => 'heading', 'content' => ['text' => 'Nasze Projekty', 'level' => 1, 'align' => 'center'], 'settings' => ['style' => ['paddingTop' => '6rem', 'paddingBottom' => '2rem']]]
                    ],
                    'en' => [
                        ['id' => 'text_1', 'type' => 'heading', 'content' => ['text' => 'Our Projects', 'level' => 1, 'align' => 'center'], 'settings' => ['style' => ['paddingTop' => '6rem', 'paddingBottom' => '2rem']]]
                    ]
                ]
            ],
            [
                'key' => '404',
                'title' => ['en' => 'Page Not Found', 'pl' => 'Strona nie znaleziona'],
                'slug' => ['en' => '404', 'pl' => '404'],
                'content' => [
                    'pl' => [
                        [
                            'id' => 'section_404', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'flexConfig' => ['gap' => '4', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'col'], 'layoutType' => 'default'],
                            'children' => [
                                ['id' => 'h404_1', 'type' => 'heading', 'content' => ['text' => '404', 'level' => 1, 'align' => 'center'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'h404_2', 'type' => 'heading', 'content' => ['text' => 'Strona nie znaleziona', 'level' => 2, 'align' => 'center'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'p404_1', 'type' => 'paragraph', 'content' => ['text' => '<p class="text-center">Przepraszamy, ale strona której szukasz nie istnieje lub została przeniesiona.</p>'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'btn404_1', 'type' => 'button', 'content' => ['label' => '← Wróć na stronę główną', 'url' => '/', 'align' => 'center', 'style' => 'primary'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                            ],
                            'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']],
                            'parent_id' => null,
                        ]
                    ],
                    'en' => [
                        [
                            'id' => 'section_404', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'flexConfig' => ['gap' => '4', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'col'], 'layoutType' => 'default'],
                            'children' => [
                                ['id' => 'h404_1', 'type' => 'heading', 'content' => ['text' => '404', 'level' => 1, 'align' => 'center'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'h404_2', 'type' => 'heading', 'content' => ['text' => 'Page Not Found', 'level' => 2, 'align' => 'center'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'p404_1', 'type' => 'paragraph', 'content' => ['text' => '<p class="text-center">Sorry, the page you are looking for does not exist or has been moved.</p>'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'btn404_1', 'type' => 'button', 'content' => ['label' => '← Back to Home', 'url' => '/', 'align' => 'center', 'style' => 'primary'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                            ],
                            'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']],
                            'parent_id' => null,
                        ]
                    ]
                ]
            ],
            [
                'key' => 'maintenance',
                'title' => ['en' => 'Maintenance Mode', 'pl' => 'Przerwa techniczna'],
                'slug' => ['en' => 'maintenance', 'pl' => 'przerwa-techniczna'],
                'content' => [
                    'pl' => [
                        [
                            'id' => 'section_maint', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'flexConfig' => ['gap' => '4', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'col'], 'layoutType' => 'default'],
                            'children' => [
                                ['id' => 'hm_1', 'type' => 'heading', 'content' => ['text' => 'Przerwa techniczna', 'level' => 1, 'align' => 'center'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'pm_1', 'type' => 'paragraph', 'content' => ['text' => '<p class="text-center">Wracamy wkrótce! Pracujemy nad ulepszeniami.</p>'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                            ],
                            'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']],
                            'parent_id' => null,
                        ]
                    ],
                    'en' => [
                        [
                            'id' => 'section_maint', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'flexConfig' => ['gap' => '4', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'col'], 'layoutType' => 'default'],
                            'children' => [
                                ['id' => 'hm_1', 'type' => 'heading', 'content' => ['text' => 'Maintenance Mode', 'level' => 1, 'align' => 'center'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'pm_1', 'type' => 'paragraph', 'content' => ['text' => '<p class="text-center">We\'ll be back soon! We\'re working on improvements.</p>'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                            ],
                            'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']],
                            'parent_id' => null,
                        ]
                    ]
                ]
            ],
            [
                'key' => 'coming_soon',
                'title' => ['en' => 'Coming Soon', 'pl' => 'Już wkrótce'],
                'slug' => ['en' => 'coming-soon', 'pl' => 'juz-wkrotce'],
                'content' => [
                    'pl' => [
                        [
                            'id' => 'section_soon', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'flexConfig' => ['gap' => '4', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'col'], 'layoutType' => 'default'],
                            'children' => [
                                ['id' => 'hs_1', 'type' => 'heading', 'content' => ['text' => 'Już wkrótce', 'level' => 1, 'align' => 'center'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'ps_1', 'type' => 'paragraph', 'content' => ['text' => '<p class="text-center">Pracujemy nad czymś niesamowitym. Wróć wkrótce!</p>'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                            ],
                            'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']],
                            'parent_id' => null,
                        ]
                    ],
                    'en' => [
                        [
                            'id' => 'section_soon', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'flexConfig' => ['gap' => '4', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'col'], 'layoutType' => 'default'],
                            'children' => [
                                ['id' => 'hs_1', 'type' => 'heading', 'content' => ['text' => 'Coming Soon', 'level' => 1, 'align' => 'center'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                                ['id' => 'ps_1', 'type' => 'paragraph', 'content' => ['text' => '<p class="text-center">We are working on something amazing. Check back soon!</p>'], 'children' => [], 'settings' => ['style' => []], 'parent_id' => null],
                            ],
                            'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']],
                            'parent_id' => null,
                        ]
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
