<?php

namespace Tests\Feature\Admin;

use App\Models\AnimationPreset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimationPresetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_store_animation_preset(): void
    {
        $payload = [
            'name' => 'Hero Reveal',
            'slug' => 'hero-reveal',
            'is_active' => true,
            'definition' => [
                'trigger' => 'onEnter',
                'preset' => 'fade-up',
                'duration' => 0.8,
                'delay' => 0.1,
                'ease' => 'power2.out',
                'once' => true,
                'timeline_id' => 'main-sequence',
                'tween' => [
                    'from' => ['opacity' => 0],
                    'to' => ['opacity' => 1],
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.animation-presets.store'), $payload);

        $response->assertRedirect(route('admin.animation-presets.index'));
        $this->assertDatabaseHas('animation_presets', [
            'slug' => 'hero-reveal',
            'name' => 'Hero Reveal',
        ]);
    }

    public function test_admin_can_update_animation_preset(): void
    {
        $preset = AnimationPreset::create([
            'name' => 'Old',
            'slug' => 'old',
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

        $response = $this->actingAs($this->admin)->put(route('admin.animation-presets.update', $preset), [
            'name' => 'Updated',
            'slug' => 'updated',
            'is_active' => false,
            'definition' => [
                'trigger' => 'onLoad',
                'preset' => 'zoom-in',
                'duration' => 1.2,
                'delay' => 0.2,
                'ease' => 'power1.out',
                'once' => false,
                'timeline_id' => '',
                'tween' => ['from' => [], 'to' => []],
            ],
        ]);

        $response->assertRedirect(route('admin.animation-presets.index'));
        $preset->refresh();
        $this->assertSame('updated', $preset->slug);
        $this->assertFalse($preset->is_active);
        $this->assertSame('zoom-in', $preset->definition['preset']);
    }

    public function test_admin_can_delete_animation_preset(): void
    {
        $preset = AnimationPreset::create([
            'name' => 'Delete me',
            'slug' => 'delete-me',
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

        $response = $this->actingAs($this->admin)->delete(route('admin.animation-presets.destroy', $preset));

        $response->assertRedirect(route('admin.animation-presets.index'));
        $this->assertDatabaseMissing('animation_presets', ['id' => $preset->id]);
    }
}
