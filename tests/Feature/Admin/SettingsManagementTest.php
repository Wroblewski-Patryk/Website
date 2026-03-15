<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;

class SettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_settings(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Settings/Index'));
    }

    public function test_admin_can_update_settings(): void
    {
        $data = [
            'site_name' => 'My Updated Site',
            'home_page_id' => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.settings.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'key' => 'site_name',
            'value' => json_encode('My Updated Site'),
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'home_page_id',
            'value' => json_encode(1),
        ]);
    }
}
