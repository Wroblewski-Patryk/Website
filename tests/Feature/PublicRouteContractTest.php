<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Taxonomy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicRouteContractTest extends TestCase
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

    public function test_named_home_route_serves_the_configured_home_page(): void
    {
        $this->seedActiveLocales();

        $homePage = Page::factory()->create([
            'title' => ['en' => 'Home'],
            'slug' => ['en' => 'home'],
            'status' => 'published',
        ]);

        Setting::query()->create(['key' => 'home_page_id', 'value' => $homePage->id]);

        $response = $this->get(route('home', ['locale' => 'en']));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Page')
            ->where('page.id', $homePage->id)
        );
    }

    public function test_named_public_content_route_resolves_a_page_path(): void
    {
        $this->seedActiveLocales();

        $aboutPage = Page::factory()->create([
            'title' => ['en' => 'About us'],
            'slug' => ['en' => 'about-us'],
            'status' => 'published',
        ]);

        $response = $this->get(route('public.content.show', [
            'locale' => 'en',
            'path' => 'about-us',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Page')
            ->where('page.id', $aboutPage->id)
        );
    }

    public function test_named_public_content_route_resolves_a_post_detail_path(): void
    {
        $this->seedActiveLocales();

        $blogArchivePage = Page::factory()->create([
            'slug' => ['en' => 'blog'],
            'title' => ['en' => 'Blog'],
            'status' => 'published',
        ]);

        Setting::query()->create(['key' => 'blog_page_id', 'value' => $blogArchivePage->id]);

        $post = Post::factory()->create([
            'slug' => ['en' => 'launch-notes'],
            'title' => ['en' => 'Launch notes'],
            'status' => 'published',
        ]);

        $response = $this->get(route('public.content.show', [
            'locale' => 'en',
            'path' => 'blog/launch-notes',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Blog/Show')
            ->where('post.id', $post->id)
        );
    }

    public function test_named_public_content_route_resolves_a_project_detail_path(): void
    {
        $this->seedActiveLocales();

        $projectsArchivePage = Page::factory()->create([
            'slug' => ['en' => 'projects'],
            'title' => ['en' => 'Projects'],
            'status' => 'published',
        ]);

        Setting::query()->create(['key' => 'projects_page_id', 'value' => $projectsArchivePage->id]);

        $taxonomy = Taxonomy::query()->create([
            'type' => 'category',
            'module' => 'projects',
            'title' => ['en' => 'Case Studies', 'pl' => 'Studia przypadkow'],
            'slug' => ['en' => 'case-studies', 'pl' => 'studia-przypadkow'],
            'description' => ['en' => 'Project category', 'pl' => 'Kategoria projektu'],
        ]);

        $project = Project::factory()->create([
            'slug' => ['en' => 'platform-redesign'],
            'title' => ['en' => 'Platform redesign'],
            'status' => 'published',
            'category' => 'Legacy category',
        ]);
        $project->taxonomies()->attach($taxonomy->id);

        $response = $this->get(route('public.content.show', [
            'locale' => 'en',
            'path' => 'projects/platform-redesign',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Project')
            ->where('project.id', $project->id)
            ->where('project.category.en', 'Case Studies')
        );
    }

    public function test_named_public_content_route_resolves_project_archive_with_taxonomy_backed_categories(): void
    {
        $this->seedActiveLocales();

        $projectsArchivePage = Page::factory()->create([
            'slug' => ['en' => 'projects'],
            'title' => ['en' => 'Projects'],
            'status' => 'published',
        ]);

        Setting::query()->create(['key' => 'projects_page_id', 'value' => $projectsArchivePage->id]);

        $taxonomy = Taxonomy::query()->create([
            'type' => 'category',
            'module' => 'projects',
            'title' => ['en' => 'Brand Systems', 'pl' => 'Systemy marki'],
            'slug' => ['en' => 'brand-systems', 'pl' => 'systemy-marki'],
            'description' => ['en' => 'Project category', 'pl' => 'Kategoria projektu'],
        ]);

        $project = Project::factory()->create([
            'title' => ['en' => 'Identity refresh'],
            'slug' => ['en' => 'identity-refresh'],
            'status' => 'published',
            'category' => 'Legacy category',
        ]);
        $project->taxonomies()->attach($taxonomy->id);

        $response = $this->get(route('public.content.show', [
            'locale' => 'en',
            'path' => 'projects',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/ProjectList')
            ->where('projects.0.id', $project->id)
            ->where('projects.0.category.en', 'Brand Systems')
            ->where('all_projects.0.category.en', 'Brand Systems')
        );
    }

    public function test_named_public_content_route_resolves_post_taxonomy_archives_only(): void
    {
        $this->seedActiveLocales();

        $taxonomy = Taxonomy::query()->create([
            'type' => 'category',
            'module' => 'posts',
            'title' => ['en' => 'News', 'pl' => 'Aktualnosci'],
            'slug' => ['en' => 'news', 'pl' => 'aktualnosci'],
            'description' => ['en' => 'Latest updates', 'pl' => 'Najnowsze aktualizacje'],
        ]);

        $post = Post::factory()->create([
            'slug' => ['en' => 'launch-notes'],
            'title' => ['en' => 'Launch notes'],
            'status' => 'published',
        ]);

        $post->taxonomies()->attach($taxonomy->id);

        $response = $this->get(route('public.content.show', [
            'locale' => 'en',
            'path' => 'category/news',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Blog/Index')
            ->where('taxonomy.id', $taxonomy->id)
            ->where('posts.data.0.id', $post->id)
        );
    }

    public function test_named_public_content_route_rejects_project_taxonomy_archives_in_v1(): void
    {
        $this->seedActiveLocales();

        Taxonomy::query()->create([
            'type' => 'category',
            'module' => 'projects',
            'title' => ['en' => 'Case Studies', 'pl' => 'Case Studies'],
            'slug' => ['en' => 'case-studies', 'pl' => 'case-studies'],
            'description' => ['en' => 'Project taxonomy', 'pl' => 'Project taxonomy'],
        ]);

        $response = $this->get(route('public.content.show', [
            'locale' => 'en',
            'path' => 'category/case-studies',
        ]));

        $response->assertNotFound();
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
