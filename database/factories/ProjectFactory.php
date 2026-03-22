<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titlePl = fake('pl_PL')->sentence(6);
        $titleEn = fake('en_US')->catchPhrase();
        return [
            'title' => ['pl' => $titlePl, 'en' => $titleEn],
            'slug' => ['pl' => Str::slug($titlePl), 'en' => Str::slug($titleEn)],
            'description' => ['pl' => fake('pl_PL')->paragraph(), 'en' => fake('en_US')->paragraph()],
            'content' => [],
            'status' => 'published',
            'client_id' => Client::factory(),
            'desktop_image' => null,
            'mobile_image' => null,
            'url' => fake()->url(),
        ];
    }
}
