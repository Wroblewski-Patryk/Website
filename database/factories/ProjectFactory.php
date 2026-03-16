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
        $title = fake()->sentence(3);
        return [
            'title' => ['pl' => $title, 'en' => $title],
            'slug' => ['pl' => Str::slug($title), 'en' => Str::slug($title)],
            'description' => ['pl' => fake()->paragraph(), 'en' => fake()->paragraph()],
            'content' => [],
            'status' => 'published',
            'client_id' => Client::factory(),
            'desktop_image' => null,
            'mobile_image' => null,
            'url' => fake()->url(),
        ];
    }
}
