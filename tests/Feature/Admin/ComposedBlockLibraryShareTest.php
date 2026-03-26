<?php

namespace Tests\Feature\Admin;

use App\Models\ComposedBlock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ComposedBlockLibraryShareTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_composed_blocks_are_shared_to_inertia_payload(): void
    {
        $admin = User::factory()->admin()->create();

        ComposedBlock::create([
            'title' => ['en' => 'Active Hero', 'pl' => 'Hero aktywny'],
            'slug' => 'active-hero',
            'content' => [],
            'is_active' => true,
        ]);

        ComposedBlock::create([
            'title' => ['en' => 'Inactive Hero', 'pl' => 'Hero nieaktywny'],
            'slug' => 'inactive-hero',
            'content' => [],
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->has('composed_blocks_library', 1)
            ->where('composed_blocks_library.0.slug', 'active-hero')
        );
    }
}

