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
                'title' => ['en' => 'Welcome to Featherly', 'pl' => 'Witaj w Featherly'],
                'slug' => ['en' => 'home', 'pl' => 'home'],
                'content' => [
                    'pl' => [
                        [
                            'id' => 'hero_section', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'flexConfig' => ['gap' => '6', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'col'], 'layoutType' => 'default'],
                            'children' => [
                                ['id' => 'hero_h1', 'type' => 'heading', 'content' => ['text' => 'Twoja Cyfrowa Kreatywność Bez Granic', 'level' => 1, 'align' => 'center'], 'settings' => ['style' => ['textColor' => 'text-primary']]],
                                ['id' => 'hero_p', 'type' => 'paragraph', 'content' => ['text' => '<p class="text-center text-xl max-w-3xl mx-auto opacity-80">Poznaj Featherly — nowoczesny system CMS, który daje Ci pełną kontrolę nad designem i treścią. Buduj piękne strony za pomocą wizualnego edytora bloków.</p>'], 'settings' => ['style' => ['marginBottom' => '2rem']]],
                                ['id' => 'hero_btns', 'type' => 'container', 'content' => ['htmlTag' => 'div', 'isBoxed' => false, 'flexConfig' => ['gap' => '4', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'row'], 'layoutType' => 'flex'], 'children' => [
                                    ['id' => 'hero_btn_1', 'type' => 'button', 'content' => ['label' => 'Rozpocznij przygodę', 'url' => '/admin', 'align' => 'center', 'style' => 'primary']],
                                    ['id' => 'hero_btn_2', 'type' => 'button', 'content' => ['label' => 'Zobacz projekty', 'url' => '/projekty', 'align' => 'center', 'style' => 'outline']],
                                ]],
                            ],
                            'settings' => ['style' => ['paddingTop' => '12rem', 'paddingBottom' => '8rem'], 'animations' => ['enabled' => true, 'preset' => 'fade-up', 'duration' => 1.2]],
                        ],
                        [
                            'id' => 'image_section', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true],
                            'children' => [
                                ['id' => 'main_image', 'type' => 'image', 'content' => ['url' => '/storage/media/bg.jpg', 'alt' => 'Featherly Dashboard Preview', 'caption' => 'Intuicyjny interfejs edytora']],
                            ],
                            'settings' => ['style' => ['paddingBottom' => '8rem'], 'animations' => ['enabled' => true, 'preset' => 'zoom-in', 'delay' => 0.4]],
                        ],
                        [
                            'id' => 'divider_1', 'type' => 'divider', 'content' => ['text' => 'NAJNOWSZE REALIZACJE'], 'settings' => ['style' => ['paddingBottom' => '4rem']]
                        ],
                        [
                            'id' => 'projects_section', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true],
                            'children' => [
                                ['id' => 'projects_list_1', 'type' => 'projects_list', 'content' => ['count' => 3, 'layout' => 'grid']],
                            ],
                            'settings' => ['style' => ['paddingBottom' => '8rem']],
                        ],
                        [
                            'id' => 'posts_section', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'style' => ['bgColor' => 'bg-base-200/50']],
                            'children' => [
                                ['id' => 'posts_h2', 'type' => 'heading', 'content' => ['text' => 'Wieści z frontu', 'level' => 2, 'align' => 'left'], 'settings' => ['style' => ['marginBottom' => '3rem']]],
                                ['id' => 'posts_list_1', 'type' => 'posts_list', 'content' => ['count' => 3, 'layout' => 'grid']],
                            ],
                            'settings' => ['style' => ['paddingTop' => '8rem', 'paddingBottom' => '8rem']],
                        ],
                    ],
                    'en' => [
                        [
                            'id' => 'hero_section', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'flexConfig' => ['gap' => '6', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'col'], 'layoutType' => 'default'],
                            'children' => [
                                ['id' => 'hero_h1', 'type' => 'heading', 'content' => ['text' => 'Your Digital Creativity Unleashed', 'level' => 1, 'align' => 'center'], 'settings' => ['style' => ['textColor' => 'text-primary']]],
                                ['id' => 'hero_p', 'type' => 'paragraph', 'content' => ['text' => '<p class="text-center text-xl max-w-3xl mx-auto opacity-80">Meet Featherly — the modern CMS that gives you full control over design and content. Build stunning websites with our visual block editor.</p>'], 'settings' => ['style' => ['marginBottom' => '2rem']]],
                                ['id' => 'hero_btns', 'type' => 'container', 'content' => ['htmlTag' => 'div', 'isBoxed' => false, 'flexConfig' => ['gap' => '4', 'wrap' => 'nowrap', 'align' => 'center', 'justify' => 'center', 'direction' => 'row'], 'layoutType' => 'flex'], 'children' => [
                                    ['id' => 'hero_btn_1', 'type' => 'button', 'content' => ['label' => 'Start Adventure', 'url' => '/admin', 'align' => 'center', 'style' => 'primary']],
                                    ['id' => 'hero_btn_2', 'type' => 'button', 'content' => ['label' => 'View Projects', 'url' => '/projects', 'align' => 'center', 'style' => 'outline']],
                                ]],
                            ],
                            'settings' => ['style' => ['paddingTop' => '12rem', 'paddingBottom' => '8rem'], 'animations' => ['enabled' => true, 'preset' => 'fade-up', 'duration' => 1.2]],
                        ],
                        [
                            'id' => 'image_section', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true],
                            'children' => [
                                ['id' => 'main_image', 'type' => 'image', 'content' => ['url' => '/storage/media/bg.jpg', 'alt' => 'Featherly Dashboard Preview', 'caption' => 'Intuitive editor interface']],
                            ],
                            'settings' => ['style' => ['paddingBottom' => '8rem'], 'animations' => ['enabled' => true, 'preset' => 'zoom-in', 'delay' => 0.4]],
                        ],
                        [
                            'id' => 'divider_1', 'type' => 'divider', 'content' => ['text' => 'LATEST REALIZATIONS'], 'settings' => ['style' => ['paddingBottom' => '4rem']]
                        ],
                        [
                            'id' => 'projects_section', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true],
                            'children' => [
                                ['id' => 'projects_list_1', 'type' => 'projects_list', 'content' => ['count' => 3, 'layout' => 'grid']],
                            ],
                            'settings' => ['style' => ['paddingBottom' => '8rem']],
                        ],
                        [
                            'id' => 'posts_section', 'type' => 'container',
                            'content' => ['htmlTag' => 'section', 'isBoxed' => true, 'style' => ['bgColor' => 'bg-base-200/50']],
                            'children' => [
                                ['id' => 'posts_h2', 'type' => 'heading', 'content' => ['text' => 'News from the front', 'level' => 2, 'align' => 'left'], 'settings' => ['style' => ['marginBottom' => '3rem']]],
                                ['id' => 'posts_list_1', 'type' => 'posts_list', 'content' => ['count' => 3, 'layout' => 'grid']],
                            ],
                            'settings' => ['style' => ['paddingTop' => '8rem', 'paddingBottom' => '8rem']],
                        ],
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
