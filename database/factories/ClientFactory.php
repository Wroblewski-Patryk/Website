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
        $namePl = fake('pl_PL')->company();
        $nameEn = fake('en_US')->company();
        return [
            'title' => ['pl' => $namePl, 'en' => $nameEn],
            'slug' => ['pl' => Str::slug($namePl), 'en' => Str::slug($nameEn)],
            'description' => ['pl' => fake('pl_PL')->sentence(), 'en' => fake('en_US')->sentence()],
            'website_url' => fake()->url(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
