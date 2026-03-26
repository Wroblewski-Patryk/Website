<?php

namespace Tests\Feature\Public;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomePageFallbackBehaviorTest extends TestCase
{
    use RefreshDatabase;

    public function test_planned_home_page_with_publish_date_renders_coming_soon_with_countdown(): void
    {
        $homePage = Page::factory()->create([
            'status' => 'planned',
            'published_at' => now()->addDays(2),
        ]);

        $comingSoonPage = Page::factory()->create([
            'status' => 'published',
            'title' => ['en' => 'Coming Soon', 'pl' => 'Wkrótce'],
            'slug' => ['en' => 'coming-soon', 'pl' => 'wkrotce'],
        ]);

        Setting::updateOrCreate(['key' => 'home_page_id'], ['value' => $homePage->id]);
        Setting::updateOrCreate(['key' => 'coming_soon_page_id'], ['value' => $comingSoonPage->id]);

        $response = $this->get('/en');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Page')
            ->where('page.id', $comingSoonPage->id)
            ->whereNotNull('coming_soon_countdown_to')
        );
    }

    public function test_non_public_unscheduled_home_page_returns_404(): void
    {
        $homePage = Page::factory()->create([
            'status' => 'draft',
            'published_at' => null,
        ]);

        Setting::updateOrCreate(['key' => 'home_page_id'], ['value' => $homePage->id]);

        $response = $this->get('/en');

        $response->assertStatus(404);
    }
}
