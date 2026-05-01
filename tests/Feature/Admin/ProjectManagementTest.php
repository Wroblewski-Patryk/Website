<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\Project;
use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_list_projects(): void
    {
        Project::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.projects.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Projects/Index'));
    }

    public function test_admin_can_search_projects_by_localized_title(): void
    {
        Project::factory()->create([
            'title' => ['en' => 'Alpha project', 'pl' => 'Alpha projekt'],
            'slug' => ['en' => 'alpha-project', 'pl' => 'alpha-projekt'],
        ]);
        $target = Project::factory()->create([
            'title' => ['en' => 'Needle project', 'pl' => 'Szukany projekt'],
            'slug' => ['en' => 'needle-project', 'pl' => 'szukany-projekt'],
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.projects.index', ['search' => 'Needle']));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Projects/Index')
            ->has('projects.data', 1)
            ->where('projects.data.0.id', $target->id)
        );
    }

    public function test_admin_project_index_prefers_taxonomy_category_over_legacy_field(): void
    {
        $project = Project::factory()->create([
            'category' => 'Legacy category',
        ]);

        $taxonomy = Taxonomy::create([
            'title' => ['en' => 'Case Studies', 'pl' => 'Studia przypadku'],
            'slug' => ['en' => 'case-studies', 'pl' => 'studia-przypadku'],
            'description' => ['en' => 'Category', 'pl' => 'Kategoria'],
            'module' => 'projects',
            'type' => 'category',
            'order' => 1,
        ]);

        $project->taxonomies()->attach($taxonomy->id);

        $response = $this->actingAs($this->admin)->get(route('admin.projects.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Projects/Index')
            ->where('projects.data.0.category.en', 'Case Studies')
            ->where('projects.data.0.category.pl', 'Studia przypadku')
        );
    }

    public function test_admin_can_create_project_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.projects.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Projects/Edit'));
    }

    public function test_admin_can_store_project(): void
    {
        $client = Client::factory()->create();
        $data = [
            'title' => ['pl' => 'Nowy Projekt', 'en' => 'New Project'],
            'slug' => ['pl' => 'nowy-projekt', 'en' => 'new-project'],
            'description' => ['pl' => 'Opis projektu', 'en' => 'Project description'],
            'status' => 'published',
            'client_id' => $client->id,
            'content' => [['block' => 'text', 'data' => ['text' => 'test']]],
            'desktop_image' => null,
            'mobile_image' => null,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.projects.store'), $data);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $this->assertEquals(1, Project::count());
        $project = Project::first();
        $this->assertEquals('Nowy Projekt', $project->getTranslation('title', 'pl'));
        $this->assertEquals($client->id, $project->client_id);
    }

    public function test_admin_can_update_project(): void
    {
        $project = Project::factory()->create([
            'status' => 'published'
        ]);

        $data = [
            'title' => ['pl' => 'Zaktualizowany Projekt', 'en' => 'Updated Project'],
            'slug' => ['pl' => 'zaktualizowany-projekt', 'en' => 'updated-project'],
            'description' => ['pl' => 'Nowy opis projektu', 'en' => 'New project description'],
            'status' => 'draft',
            'content' => [['block' => 'text', 'data' => ['text' => 'updated']]],
            'desktop_image' => null,
            'mobile_image' => null,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.projects.update', $project), $data);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $project->refresh();
        $this->assertEquals('Zaktualizowany Projekt', $project->getTranslation('title', 'pl'));
        $this->assertEquals('draft', $project->status);
    }

    public function test_admin_project_edit_payload_hides_legacy_category_field(): void
    {
        $project = Project::factory()->create([
            'category' => 'Legacy category',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.projects.edit', $project));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->component('Admin/Projects/Edit'));

        $page = $response->viewData('page');

        $this->assertArrayHasKey('project', $page['props']);
        $this->assertArrayNotHasKey('category', $page['props']['project']);
    }

    public function test_admin_can_delete_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.projects.destroy', $project));

        $response->assertRedirect();
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
