<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();
        return [
            'title' => ['pl' => $name, 'en' => $name],
            'slug' => ['pl' => Str::slug($name), 'en' => Str::slug($name)],
            'description' => ['pl' => fake()->sentence(), 'en' => fake()->sentence()],
            'website_url' => fake()->url(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
