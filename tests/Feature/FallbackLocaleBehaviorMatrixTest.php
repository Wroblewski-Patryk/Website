<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FallbackLocaleBehaviorMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.fallback_locale' => 'en']);

        if (DB::getDriverName() === 'sqlite') {
            DB::connection()->getPdo()->sqliteCreateFunction('json_unquote', fn ($value) => $value, 1);
        }
    }

    public function test_page_route_resolves_with_fallback_locale_slug(): void
    {
        $this->seedActiveLocales();

        Page::factory()->create([
            'slug' => ['en' => 'about-us'],
            'title' => ['en' => 'About us'],
            'status' => 'published',
        ]);

        $response = $this->get('/pl/about-us');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Page')
        );
    }

    public function test_post_archive_route_resolves_with_fallback_locale_slugs(): void
    {
        $this->seedActiveLocales();

        $blogArchivePage = Page::factory()->create([
            'slug' => ['en' => 'blog'],
            'title' => ['en' => 'Blog'],
            'status' => 'published',
        ]);

        Setting::query()->create(['key' => 'blog_page_id', 'value' => $blogArchivePage->id]);

        Post::factory()->create([
            'slug' => ['en' => 'launch-notes'],
            'title' => ['en' => 'Launch notes'],
            'status' => 'published',
        ]);

        $response = $this->get('/pl/blog/launch-notes');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Blog/Show')
        );
    }

    public function test_project_archive_route_resolves_with_fallback_locale_slugs(): void
    {
        $this->seedActiveLocales();

        $projectsArchivePage = Page::factory()->create([
            'slug' => ['en' => 'projects'],
            'title' => ['en' => 'Projects'],
            'status' => 'published',
        ]);

        Setting::query()->create(['key' => 'projects_page_id', 'value' => $projectsArchivePage->id]);

        Project::factory()->create([
            'slug' => ['en' => 'platform-redesign'],
            'title' => ['en' => 'Platform redesign'],
            'status' => 'published',
        ]);

        $response = $this->get('/pl/projects/platform-redesign');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Project')
        );
    }

    protected function seedActiveLocales(): void
    {
        Language::query()->create([
            'name' => 'English',
            'code' => 'en',
            'is_active' => true,
            'is_default' => true,
        ]);

        Language::query()->create([
            'name' => 'Polish',
            'code' => 'pl',
            'is_active' => true,
            'is_default' => false,
        ]);
    }
}
