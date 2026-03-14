<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Template>
 */
class TemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => [
                'pl' => fake()->words(2, true) . ' (PL)',
                'en' => fake()->words(2, true) . ' (EN)',
            ],
            'type' => fake()->randomElement(['header', 'footer', 'sidebar', 'page']),
            'content' => [
                [
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'type' => 'group',
                    'content' => []
                ]
            ],
            'is_active' => true,
            'is_default' => false,
        ];
    }
}
