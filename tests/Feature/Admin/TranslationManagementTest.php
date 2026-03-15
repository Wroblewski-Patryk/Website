<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Language;

class TranslationManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_list_languages(): void
    {
        Language::create(['name' => 'Polish', 'code' => 'pl', 'is_active' => true, 'is_default' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.languages.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Languages/Index'));
    }

    public function test_admin_can_store_language(): void
    {
        $data = [
            'name' => 'English',
            'code' => 'en',
            'is_active' => true,
            'is_default' => false,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.languages.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('languages', ['code' => 'en']);
    }
}
