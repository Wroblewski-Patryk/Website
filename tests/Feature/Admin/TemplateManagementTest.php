<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Template;

class TemplateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_list_templates(): void
    {
        Template::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.templates.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Templates/Index'));
    }

    public function test_admin_can_store_template(): void
    {
        $data = [
            'title' => ['pl' => 'Nagłówek', 'en' => 'Custom Header'],
            'type' => 'header',
            'content' => [['type' => 'text', 'body' => 'Test']],
            'is_active' => true,
            'is_default' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.templates.store'), $data);

        $response->assertRedirect(); // Redirects to edit
        $template = Template::all()->last();
        $this->assertEquals('Custom Header', $template->getTranslation('title', 'en'));
        $this->assertTrue($template->is_default);
    }

    public function test_template_store_validation_rejects_unknown_type(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.templates.store'), [
            'title' => ['en' => 'Custom Header'],
            'type' => 'widget',
            'content' => [['type' => 'text', 'body' => 'Test']],
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_admin_can_update_template(): void
    {
        $template = Template::factory()->create(['title' => ['en' => 'Old Name']]);

        $data = [
            'title' => ['pl' => 'Nowa nazwa', 'en' => 'Updated Name'],
            'type' => $template->type,
            'content' => [['type' => 'text', 'body' => 'Updated']],
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.templates.update', $template), $data);

        $response->assertRedirect(); // Redirects back
        $template->refresh();
        $this->assertEquals('Updated Name', $template->getTranslation('title', 'en'));
    }

    public function test_admin_can_delete_template(): void
    {
        $template = Template::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.templates.destroy', $template));

        $response->assertRedirect(); // Redirects back
        $this->assertDatabaseMissing('templates', ['id' => $template->id]);
    }

    public function test_editor_cannot_manage_templates(): void
    {
        $editor = User::factory()->editor()->create();

        $response = $this->actingAs($editor)->get(route('admin.templates.index'));

        $response->assertForbidden();
    }
}
