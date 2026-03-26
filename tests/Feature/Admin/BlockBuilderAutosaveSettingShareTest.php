<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BlockBuilderAutosaveSettingShareTest extends TestCase
{
    use RefreshDatabase;

    public function test_autosave_interval_setting_is_shared_to_admin_frontend(): void
    {
        $admin = User::factory()->admin()->create();

        Setting::updateOrCreate(
            ['key' => 'builder_autosave_interval_minutes'],
            ['value' => 7]
        );

        $response = $this->actingAs($admin)->get(route('admin.dashboard.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->where('builder_settings.autosave_interval_minutes', 7)
        );
    }
}
