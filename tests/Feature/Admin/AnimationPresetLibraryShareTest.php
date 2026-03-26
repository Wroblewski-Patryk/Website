<?php

namespace Tests\Feature\Admin;

use App\Models\AnimationPreset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AnimationPresetLibraryShareTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_animation_presets_are_shared_to_inertia_payload(): void
    {
        $admin = User::factory()->admin()->create();

        AnimationPreset::create([
            'name' => 'Active preset',
            'slug' => 'active-preset',
            'definition' => [
                'trigger' => 'onEnter',
                'preset' => 'fade-up',
                'duration' => 0.8,
                'delay' => 0,
                'ease' => 'power2.out',
                'once' => true,
                'timeline_id' => '',
                'tween' => ['from' => [], 'to' => []],
            ],
            'is_active' => true,
        ]);

        AnimationPreset::create([
            'name' => 'Inactive preset',
            'slug' => 'inactive-preset',
            'definition' => [
                'trigger' => 'onEnter',
                'preset' => 'fade-up',
                'duration' => 0.8,
                'delay' => 0,
                'ease' => 'power2.out',
                'once' => true,
                'timeline_id' => '',
                'tween' => ['from' => [], 'to' => []],
            ],
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->has('animation_presets_library', 1)
            ->where('animation_presets_library.0.slug', 'active-preset')
        );
    }
}
