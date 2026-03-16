<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_admin_can_delete_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.projects.destroy', $project));

        $response->assertRedirect();
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
