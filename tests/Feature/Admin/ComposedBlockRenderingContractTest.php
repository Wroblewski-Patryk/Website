<?php

namespace Tests\Feature\Admin;

use App\Models\ComposedBlock;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ComposedBlockRenderingContractTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_composed_blocks_library_exposes_content_for_runtime_resolution(): void
    {
        ComposedBlock::create([
            'title' => ['en' => 'Hero Bundle', 'pl' => 'Hero Bundle'],
            'slug' => 'hero-bundle',
            'content' => [
                ['id' => 'heading-1', 'type' => 'heading', 'content' => ['text' => 'Hero heading']],
            ],
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->has('composed_blocks_library', 1)
            ->where('composed_blocks_library.0.slug', 'hero-bundle')
            ->where('composed_blocks_library.0.content.0.type', 'heading')
        );
    }

    public function test_page_store_persists_composed_block_reference_payload(): void
    {
        $composedBlock = ComposedBlock::create([
            'title' => ['en' => 'Hero Bundle', 'pl' => 'Hero Bundle'],
            'slug' => 'hero-bundle',
            'content' => [
                ['id' => 'heading-1', 'type' => 'heading', 'content' => ['text' => 'Hero heading']],
            ],
            'is_active' => true,
        ]);

        $payload = [
            'title' => ['en' => 'Landing', 'pl' => 'Landing'],
            'slug' => ['en' => 'landing', 'pl' => 'landing'],
            'content' => [
                [
                    'id' => 'composed-ref-1',
                    'type' => 'composed_block',
                    'content' => [
                        'composed_block_id' => $composedBlock->id,
                        'snapshot_title' => 'Hero Bundle',
                    ],
                    'children' => [],
                ],
            ],
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.pages.store'), $payload);
        $response->assertRedirect();

        $page = Page::query()->latest('id')->first();
        $this->assertNotNull($page);
        $this->assertSame('composed_block', $page->content[0]['type']);
        $this->assertSame($composedBlock->id, $page->content[0]['content']['composed_block_id']);
    }
}

