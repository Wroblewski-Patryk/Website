<?php

namespace Tests\Feature\Admin;

use App\Models\ComposedBlock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ComposedBlockManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_store_composed_block(): void
    {
        $payload = [
            'title' => ['en' => 'Hero Section', 'pl' => 'Sekcja Hero'],
            'slug' => 'hero-section',
            'content' => [
                ['id' => 'block-1', 'type' => 'heading', 'content' => ['text' => 'Hello']],
            ],
            'settings' => ['variant' => 'marketing'],
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.composed-blocks.store'), $payload);

        $block = ComposedBlock::query()->latest('id')->first();
        $response->assertRedirect(route('admin.composed-blocks.edit', $block));
        $this->assertDatabaseHas('composed_blocks', [
            'id' => $block->id,
            'slug' => 'hero-section',
        ]);
    }

    public function test_blocks_module_route_renders_blocks_page_component(): void
    {
        ComposedBlock::create([
            'title' => ['en' => 'Hero', 'pl' => 'Hero'],
            'slug' => 'hero',
            'content' => [],
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.blocks.index'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Blocks'));
    }

    public function test_admin_can_update_composed_block(): void
    {
        $block = ComposedBlock::create([
            'title' => ['en' => 'Old Hero', 'pl' => 'Stary Hero'],
            'slug' => 'old-hero',
            'content' => [],
            'is_active' => true,
        ]);

        $payload = [
            'title' => ['en' => 'Updated Hero', 'pl' => 'Zaktualizowany Hero'],
            'slug' => 'updated-hero',
            'content' => [
                ['id' => 'block-2', 'type' => 'paragraph', 'content' => ['text' => 'Updated']],
            ],
            'settings' => ['variant' => 'landing'],
            'is_active' => false,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.composed-blocks.update', $block), $payload);

        $response->assertRedirect(route('admin.composed-blocks.edit', $block));
        $block->refresh();
        $this->assertSame('updated-hero', $block->slug);
        $this->assertFalse($block->is_active);
    }

    public function test_updating_composed_block_creates_revision_snapshot(): void
    {
        $block = ComposedBlock::create([
            'title' => ['en' => 'Old Hero', 'pl' => 'Stary Hero'],
            'slug' => 'old-hero',
            'content' => [
                ['id' => 'block-1', 'type' => 'heading', 'content' => ['text' => 'Before update']],
            ],
            'is_active' => true,
        ]);

        $payload = [
            'title' => ['en' => 'Updated Hero', 'pl' => 'Zaktualizowany Hero'],
            'slug' => 'updated-hero',
            'content' => [
                ['id' => 'block-2', 'type' => 'paragraph', 'content' => ['text' => 'After update']],
            ],
            'settings' => [],
            'is_active' => true,
        ];

        $this->actingAs($this->admin)->put(route('admin.composed-blocks.update', $block), $payload);

        $this->assertDatabaseHas('revisions', [
            'revisionable_id' => $block->id,
            'revisionable_type' => ComposedBlock::class,
        ]);
    }

    public function test_composed_block_update_rejects_stale_optimistic_lock(): void
    {
        $block = ComposedBlock::create([
            'title' => ['en' => 'Hero', 'pl' => 'Hero'],
            'slug' => 'hero',
            'content' => [['id' => 'block-1', 'type' => 'heading', 'content' => ['text' => 'Before']]],
            'is_active' => true,
        ]);

        $staleLock = now()->subHour()->toIso8601String();

        $payload = [
            'title' => ['en' => 'Hero Updated', 'pl' => 'Hero Updated'],
            'slug' => 'hero-updated',
            'content' => [['id' => 'block-2', 'type' => 'heading', 'content' => ['text' => 'After']]],
            'settings' => [],
            'is_active' => true,
            'optimistic_lock' => $staleLock,
        ];

        $response = $this->actingAs($this->admin)->from(route('admin.composed-blocks.edit', $block))->put(route('admin.composed-blocks.update', $block), $payload);

        $response->assertRedirect(route('admin.composed-blocks.edit', $block));
        $response->assertSessionHasErrors('optimistic_lock');
    }

    public function test_admin_can_delete_composed_block(): void
    {
        $block = ComposedBlock::create([
            'title' => ['en' => 'Delete Me', 'pl' => 'Usuń mnie'],
            'slug' => 'delete-me',
            'content' => [],
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.composed-blocks.destroy', $block));

        $response->assertRedirect(route('admin.composed-blocks.index'));
        $this->assertDatabaseMissing('composed_blocks', ['id' => $block->id]);
    }
}
