<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AutosaveOptimisticLockTest extends TestCase
{
    use RefreshDatabase;

    public function test_stale_optimistic_lock_is_rejected_for_page_update_autosave_payload(): void
    {
        $admin = User::factory()->admin()->create();
        $page = Page::factory()->create([
            'title' => ['en' => 'Original title', 'pl' => 'Original title'],
            'slug' => ['en' => 'original-title', 'pl' => 'original-title'],
            'content' => [['type' => 'paragraph', 'content' => ['text' => 'Initial']]],
            'status' => 'published',
            'updated_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => ['en' => 'Autosave title', 'pl' => 'Autosave title'],
            'slug' => ['en' => 'autosave-title', 'pl' => 'autosave-title'],
            'content' => [['type' => 'paragraph', 'content' => ['text' => 'Autosave update']]],
            'status' => 'published',
            'optimistic_lock' => Carbon::now()->subDay()->toIso8601String(),
        ]);

        $response->assertSessionHasErrors(['optimistic_lock']);
    }
}
