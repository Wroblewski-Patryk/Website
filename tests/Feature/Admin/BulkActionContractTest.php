<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkActionContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_publish_multiple_pages_via_bulk_contract(): void
    {
        $admin = User::factory()->admin()->create();
        $pages = Page::factory()->count(2)->create([
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.pages.bulk-action'), [
            'action' => 'publish',
            'ids' => $pages->pluck('id')->all(),
        ]);

        $response->assertOk()
            ->assertJsonPath('data.action', 'publish')
            ->assertJsonPath('data.count', 2);

        foreach ($pages as $page) {
            $this->assertDatabaseHas('pages', [
                'id' => $page->id,
                'status' => 'published',
            ]);
        }

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'content.bulk_action',
            'module' => 'content',
            'action' => 'bulk_action',
        ]);
    }

    public function test_bulk_contract_validates_payload_shape(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson(route('admin.pages.bulk-action'), [
            'action' => 'invalid',
            'ids' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['action', 'ids']);
    }

    public function test_bulk_contract_enforces_permission_middleware(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();

        $response = $this->actingAs($user)->postJson(route('admin.pages.bulk-action'), [
            'action' => 'archive',
            'ids' => [$page->id],
        ]);

        $response->assertStatus(403);
    }
}
