<?php

namespace Tests\Feature\Admin;

use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'update_available' => true,
            'checked_at' => now()->toIso8601String(),
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Settings/Index')
            ->where('updateStatus.current_version', '1.0.0')
            ->where('updateStatus.latest_version', '1.1.0')
            ->where('updateStatus.update_available', true)
            ->where('updateStatus.effective_driver', 'manual')
            ->has('updateStatus.driver_options', 3)
        );
    }

    public function test_admin_can_update_settings(): void
    {
        $data = [
            'site_name' => 'My Updated Site',
            'home_page_id' => 1,
            'update_checks_enabled' => true,
            'auto_update_enabled' => false,
            'update_release_channel' => 'stable',
            'preferred_update_driver' => 'manual',
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

        $this->assertDatabaseHas('settings', [
            'key' => 'update_checks_enabled',
            'value' => json_encode(true),
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'preferred_update_driver',
            'value' => json_encode('manual'),
        ]);
    }

    public function test_admin_can_trigger_manual_update_check_from_settings(): void
    {
        config()->set('updates.manifest_url', 'https://updates.example.test/manifest.json');
        config()->set('updates.current_version', '1.0.0');

        Setting::updateOrCreate(['key' => 'update_checks_enabled'], ['value' => false]);

        Http::fake([
            'https://updates.example.test/manifest.json' => Http::response([
                'channels' => [
                    'stable' => [
                        'latest_version' => '1.2.0',
                        'minimum_php_version' => '8.2.0',
                        'release_notes_url' => 'https://example.test/releases/1.2.0',
                        'manual_review_required' => false,
                    ],
                ],
            ]),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.check-updates'));

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success', 'admin.settings.update_check_complete');

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('1.2.0', $status['latest_version']);
        $this->assertSame('available', $status['status']);
        $this->assertFalse(Setting::query()->where('key', 'update_checks_enabled')->firstOrFail()->value);
    }

    public function test_admin_can_record_manual_update_apply_instructions(): void
    {
        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'manual']);
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.2.0',
            'release_notes_url' => 'https://example.test/releases/1.2.0',
            'update_available' => true,
            'manual_review_required' => false,
            'php_requirement_ok' => true,
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.apply-update'));

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success', 'admin.settings.update_apply_recorded');

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('manual_required', $status['apply_status']);
        $this->assertSame('manual', $status['effective_driver']);
        $this->assertNotEmpty($status['operator_instructions']);
        $this->assertTrue($status['update_available']);
    }
}
