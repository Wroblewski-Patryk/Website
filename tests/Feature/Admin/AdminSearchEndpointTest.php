<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSearchEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_search_endpoint_returns_typed_results_for_base_modules(): void
    {
        $admin = User::factory()->admin()->create();

        Page::factory()->create([
            'title' => ['en' => 'Needle page', 'pl' => 'Needle strona'],
            'slug' => ['en' => 'needle-page', 'pl' => 'needle-strona'],
            'status' => 'published',
        ]);

        Post::factory()->create([
            'title' => ['en' => 'Needle post', 'pl' => 'Needle wpis'],
            'slug' => ['en' => 'needle-post', 'pl' => 'needle-wpis'],
            'status' => 'published',
        ]);

        Project::factory()->create([
            'title' => ['en' => 'Needle project', 'pl' => 'Needle projekt'],
            'slug' => ['en' => 'needle-project', 'pl' => 'needle-projekt'],
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.search.index', [
            'query' => 'Needle',
            'limit' => 10,
        ]));

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.meta.query', 'Needle');

        $types = collect($response->json('data.results'))->pluck('type')->values()->all();
        $this->assertContains('page', $types);
        $this->assertContains('post', $types);
        $this->assertContains('project', $types);
    }
}
