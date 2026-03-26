<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Services\AdminSearch\Providers\PageSearchProvider;
use App\Services\AdminSearch\Providers\PostSearchProvider;
use App\Services\AdminSearch\Providers\ProjectSearchProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSearchProvidersTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_search_provider_returns_normalized_page_result(): void
    {
        Page::factory()->create([
            'title' => ['en' => 'Needle page', 'pl' => 'Needle strona'],
            'slug' => ['en' => 'needle-page', 'pl' => 'needle-strona'],
            'status' => 'published',
        ]);

        $results = app(PageSearchProvider::class)->search('Needle', 5);

        $this->assertNotEmpty($results);
        $this->assertSame('page', $results[0]->type);
        $this->assertStringContainsString('/admin/pages/', $results[0]->url);
    }

    public function test_post_search_provider_returns_normalized_post_result(): void
    {
        Post::factory()->create([
            'title' => ['en' => 'Needle post', 'pl' => 'Needle wpis'],
            'slug' => ['en' => 'needle-post', 'pl' => 'needle-wpis'],
            'status' => 'published',
        ]);

        $results = app(PostSearchProvider::class)->search('Needle', 5);

        $this->assertNotEmpty($results);
        $this->assertSame('post', $results[0]->type);
        $this->assertStringContainsString('/admin/posts/', $results[0]->url);
    }

    public function test_project_search_provider_returns_normalized_project_result(): void
    {
        Project::factory()->create([
            'title' => ['en' => 'Needle project', 'pl' => 'Needle projekt'],
            'slug' => ['en' => 'needle-project', 'pl' => 'needle-projekt'],
            'status' => 'published',
        ]);

        $results = app(ProjectSearchProvider::class)->search('Needle', 5);

        $this->assertNotEmpty($results);
        $this->assertSame('project', $results[0]->type);
        $this->assertStringContainsString('/admin/projects/', $results[0]->url);
    }

    public function test_page_search_provider_prioritizes_exact_match_and_status_boost(): void
    {
        Page::factory()->create([
            'title' => ['en' => 'Needle', 'pl' => 'Needle'],
            'slug' => ['en' => 'needle-exact', 'pl' => 'needle-exact'],
            'status' => 'draft',
        ]);

        Page::factory()->create([
            'title' => ['en' => 'Needle example', 'pl' => 'Needle example'],
            'slug' => ['en' => 'needle-prefix', 'pl' => 'needle-prefix'],
            'status' => 'published',
        ]);

        Page::factory()->create([
            'title' => ['en' => 'Something with Needle inside', 'pl' => 'Something with Needle inside'],
            'slug' => ['en' => 'contains-needle', 'pl' => 'contains-needle'],
            'status' => 'published',
        ]);

        $results = app(PageSearchProvider::class)->search('Needle', 10);

        $this->assertNotEmpty($results);
        $this->assertSame('Needle', $results[0]->title);
        $this->assertGreaterThan($results[1]->score, $results[0]->score);
        $this->assertGreaterThan($results[2]->score, $results[1]->score);
    }
}
