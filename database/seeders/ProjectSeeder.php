<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \App\Models\Project::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $projects = [
            [
                'title' => [
                    'pl' => 'Portal E-commerce Premium',
                    'en' => 'Premium E-commerce Portal'
                ],
                'slug' => [
                    'pl' => 'portal-ecommerce-premium-v3',
                    'en' => 'premium-ecommerce-portal-v3'
                ],
                'description' => [
                    'pl' => 'Nowoczesny portal sprzedażowy dla branży modowej.',
                    'en' => 'Modern sales portal for the fashion industry.'
                ],
                'status' => 'published',
                'content' => [
                    'pl' => [
                        [
                            'id' => 'block_1',
                            'type' => 'heading',
                            'content' => ['text' => 'Szczegóły projektu', 'level' => 2],
                            'settings' => ['style' => ['paddingTop' => '4rem']]
                        ],
                        [
                            'id' => 'block_2',
                            'type' => 'paragraph',
                            'content' => ['text' => 'Nasze rozwiązanie pozwoliło klientowi zwiększyć sprzedaż o 40% w pierwszych trzech miesiącach.'],
                            'settings' => ['style' => ['paddingBottom' => '4rem']]
                        ]
                    ],
                    'en' => [
                        [
                            'id' => 'block_1',
                            'type' => 'heading',
                            'content' => ['text' => 'Project Details', 'level' => 2],
                            'settings' => ['style' => ['paddingTop' => '4rem']]
                        ],
                        [
                            'id' => 'block_2',
                            'type' => 'paragraph',
                            'content' => ['text' => 'Our solution allowed the client to increase sales by 40% in the first three months.'],
                            'settings' => ['style' => ['paddingBottom' => '4rem']]
                        ]
                    ]
                ]
            ],
            [
                'title' => [
                    'pl' => 'Aplikacja Mobilna Active',
                    'en' => 'Active Mobile App'
                ],
                'slug' => [
                    'pl' => 'aplikacja-active-v3',
                    'en' => 'active-app-v3'
                ],
                'description' => [
                    'pl' => 'Aplikacja do śledzenia treningów i diety.',
                    'en' => 'App for tracking workouts and diet.'
                ],
                'status' => 'published',
                'content' => [
                    'pl' => [
                        [
                            'id' => 'block_1',
                            'type' => 'hero',
                            'content' => [
                                'headline' => 'Trenuj mądrzej',
                                'subheadline' => 'Wszystko w Twoim telefonie.',
                            ],
                            'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']]
                        ]
                    ],
                    'en' => [
                        [
                            'id' => 'block_1',
                            'type' => 'hero',
                            'content' => [
                                'headline' => 'Train smarter',
                                'subheadline' => 'Everything in your phone.',
                            ],
                            'settings' => ['style' => ['paddingTop' => '10rem', 'paddingBottom' => '10rem']]
                        ]
                    ]
                ]
            ]
        ];

        foreach ($projects as $p) {
            $slugPl = $p['slug']['pl'];
            \App\Models\Project::updateOrCreate(
                ['slug->pl' => $slugPl],
                $p
            );
        }
    }
}
