<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = \App\Models\User::factory()->admin()->create();
    }

    public function test_admin_can_list_pages(): void
    {
        \App\Models\Page::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.pages.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Pages/Index'));
    }

    public function test_admin_can_search_pages_by_localized_title(): void
    {
        \App\Models\Page::factory()->create([
            'title' => ['en' => 'Alpha landing', 'pl' => 'Alpha strona'],
            'slug' => ['en' => 'alpha-landing', 'pl' => 'alpha-strona'],
        ]);
        $target = \App\Models\Page::factory()->create([
            'title' => ['en' => 'Needle page', 'pl' => 'Szukana strona'],
            'slug' => ['en' => 'needle-page', 'pl' => 'szukana-strona'],
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pages.index', ['search' => 'Needle']));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Pages/Index')
            ->has('pages.data', 1)
            ->where('pages.data.0.id', $target->id)
        );
    }

    public function test_admin_can_view_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pages.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Pages/Edit'));
    }

    public function test_admin_can_store_page(): void
    {
        $data = [
            'title' => ['pl' => 'Testowa strona', 'en' => 'Test page'],
            'slug' => ['pl' => 'testowa-strona', 'en' => 'test-page'],
            'content' => [['type' => 'paragraph', 'content' => ['text' => 'Initial content']]],
            'status' => 'published',
            'published_at' => now()->toDateTimeString(),
            'meta_title' => ['pl' => 'TytuÄąâ€š SEO', 'en' => 'SEO Title'],
            'meta_description' => ['pl' => 'Opis SEO', 'en' => 'SEO Description'],
            'canonical_url' => 'https://example.com/canonical-page',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.pages.store'), $data);

        $response->assertRedirect(); // Redirects to edit page
        $this->assertDatabaseHas('pages', [
            'id' => 1,
            'title->pl' => 'Testowa strona',
            'slug->pl' => 'testowa-strona',
            'meta_title->pl' => 'TytuÄąâ€š SEO',
            'meta_description->pl' => 'Opis SEO',
            'canonical_url' => 'https://example.com/canonical-page',
        ]);
    }

    public function test_admin_can_update_page(): void
    {
        $page = \App\Models\Page::factory()->create([
            'title' => ['pl' => 'Stary tytuÄąâ€š', 'en' => 'Old title']
        ]);

        $data = [
            'title' => ['pl' => 'Nowy tytuÄąâ€š', 'en' => 'New title'],
            'slug' => ['pl' => 'nowy-tytul', 'en' => 'new-title'],
            'content' => [['type' => 'paragraph', 'content' => ['text' => 'Updated content']]],
            'status' => 'published',
        ];

        // Testing PUT method specifically as it was causing issues before
        $response = $this->actingAs($this->admin)->put(route('admin.pages.update', $page), $data);

        $response->assertRedirect(); // Redirects back or to edit
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title->pl' => 'Nowy tytuÄąâ€š',
        ]);
    }

    public function test_update_creates_revision(): void
    {
        $page = \App\Models\Page::factory()->create([
            'content' => [['type' => 'paragraph', 'content' => ['text' => 'Initial content']]]
        ]);

        $data = [
            'title' => $page->getTranslations('title'),
            'slug' => $page->getTranslations('slug'),
            'content' => [['type' => 'paragraph', 'content' => ['text' => 'Modified content']]],
            'status' => 'published',
        ];

        $this->actingAs($this->admin)->put(route('admin.pages.update', $page), $data);

        $this->assertDatabaseHas('revisions', [
            'revisionable_id' => $page->id,
            'revisionable_type' => \App\Models\Page::class ,
        ]);

        $this->assertEquals(1, $page->revisions()->count());
    }

    public function test_admin_can_delete_page(): void
    {
        $page = \App\Models\Page::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.pages.destroy', $page));

        $response->assertRedirect();
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }
    public function test_page_store_persists_media_picker_ids_and_order_in_block_content(): void
    {
        $data = [
            'title' => ['pl' => 'Strona media', 'en' => 'Media page'],
            'slug' => ['pl' => 'strona-media', 'en' => 'media-page'],
            'content' => [
                [
                    'id' => 'block-image-1',
                    'type' => 'image',
                    'content' => [
                        'media_id' => 77,
                        'url' => 'https://cdn.example.com/hero.jpg',
                        'alt' => 'Hero',
                    ],
                    'children' => [],
                ],
                [
                    'id' => 'block-carousel-1',
                    'type' => 'carousel',
                    'content' => [
                        'image_ids' => [31, 22, 45],
                        'images' => [
                            'https://cdn.example.com/31.jpg',
                            'https://cdn.example.com/22.jpg',
                            'https://cdn.example.com/45.jpg',
                        ],
                    ],
                    'children' => [],
                ],
            ],
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.pages.store'), $data);
        $response->assertRedirect();

        $page = \App\Models\Page::query()->latest('id')->first();
        $this->assertNotNull($page);

        $storedContent = $page->content;
        $this->assertIsArray($storedContent);
        $this->assertSame(77, $storedContent[0]['content']['media_id']);
        $this->assertSame([31, 22, 45], $storedContent[1]['content']['image_ids']);
    }
}
