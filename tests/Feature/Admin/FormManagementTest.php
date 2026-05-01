<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Form;

class FormManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_list_forms(): void
    {
        Form::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.forms.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Forms/Index'));
    }

    public function test_admin_can_store_form(): void
    {
        $data = [
            'title' => ['pl' => 'Kontakt', 'en' => 'Contact Us'],
            'content' => [['type' => 'text', 'label' => 'Name']],
            'settings' => ['submit_text' => 'Send'],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.forms.store'), $data);

        $form = Form::all()->last();
        $response->assertRedirect(route('admin.forms.edit', $form));
        $this->assertEquals('Contact Us', $form->getTranslation('title', 'en'));
    }

    public function test_form_store_validation_requires_content_array(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.forms.store'), [
            'title' => ['en' => 'Contact Us'],
            'content' => 'not-an-array',
        ]);

        $response->assertSessionHasErrors('content');
    }

    public function test_admin_can_update_form(): void
    {
        $form = Form::factory()->create();

        $data = [
            'title' => ['pl' => 'Zaktualizowany', 'en' => 'Updated Form'],
            'content' => $form->content,
            'settings' => $form->settings,
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.forms.edit', $form))
            ->put(route('admin.forms.update', $form), $data);

        $response->assertRedirect(route('admin.forms.edit', $form));
        $form->refresh();
        $this->assertEquals('Updated Form', $form->getTranslation('title', 'en'));
    }

    public function test_admin_can_delete_form(): void
    {
        $form = Form::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.forms.destroy', $form));

        $response->assertRedirect();
        $this->assertDatabaseMissing('forms', ['id' => $form->id]);
    }

    public function test_editor_cannot_manage_forms(): void
    {
        $editor = User::factory()->editor()->create();

        $response = $this->actingAs($editor)->get(route('admin.forms.index'));

        $response->assertForbidden();
    }
}
