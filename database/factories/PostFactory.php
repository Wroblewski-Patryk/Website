<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titlePl = fake('pl_PL')->sentence();
        $titleEn = fake('en_US')->sentence();
        return [
            'title' => ['pl' => $titlePl, 'en' => $titleEn],
            'slug' => ['pl' => \Illuminate\Support\Str::slug($titlePl), 'en' => \Illuminate\Support\Str::slug($titleEn)],
            'content' => [
                'pl' => fake('pl_PL')->paragraph(10), 
                'en' => fake('en_US')->realText(500)
            ],
            'excerpt' => [
                'pl' => fake('pl_PL')->sentence(), 
                'en' => fake('en_US')->sentence()
            ],
            'status' => 'published',
            'published_at' => now(),
        ];
    }
}
