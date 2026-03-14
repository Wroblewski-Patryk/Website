<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = \App\Models\User::factory()->create();
    }

    public function test_admin_can_list_pages(): void
    {
        \App\Models\Page::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.pages.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Pages/Index'));
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
            'meta_title' => ['pl' => 'Tytuł SEO', 'en' => 'SEO Title'],
            'meta_description' => ['pl' => 'Opis SEO', 'en' => 'SEO Description'],
            'canonical_url' => 'https://example.com/canonical-page',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.pages.store'), $data);

        $response->assertRedirect(); // Redirects to edit page
        $this->assertDatabaseHas('pages', [
            'id' => 1,
            'title->pl' => 'Testowa strona',
            'slug->pl' => 'testowa-strona',
            'meta_title->pl' => 'Tytuł SEO',
            'meta_description->pl' => 'Opis SEO',
            'canonical_url' => 'https://example.com/canonical-page',
        ]);
    }

    public function test_admin_can_update_page(): void
    {
        $page = \App\Models\Page::factory()->create([
            'title' => ['pl' => 'Stary tytuł', 'en' => 'Old title']
        ]);

        $data = [
            'title' => ['pl' => 'Nowy tytuł', 'en' => 'New title'],
            'slug' => ['pl' => 'nowy-tytul', 'en' => 'new-title'],
            'content' => [['type' => 'paragraph', 'content' => ['text' => 'Updated content']]],
            'status' => 'published',
        ];

        // Testing PUT method specifically as it was causing issues before
        $response = $this->actingAs($this->admin)->put(route('admin.pages.update', $page), $data);

        $response->assertRedirect(); // Redirects back or to edit
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title->pl' => 'Nowy tytuł',
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
}
