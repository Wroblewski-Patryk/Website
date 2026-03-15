<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_list_posts(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.posts.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Posts/Index'));
    }

    public function test_admin_can_store_post(): void
    {
        $data = [
            'title' => ['pl' => 'Test Post', 'en' => 'Test Post'],
            'slug' => ['pl' => 'test-post', 'en' => 'test-post'],
            'content' => ['pl' => 'Treść posta', 'en' => 'Post content'],
            'excerpt' => ['pl' => 'Excerpt', 'en' => 'Excerpt'],
            'status' => 'published',
            'published_at' => now()->toDateTimeString(),
            'meta_title' => ['pl' => 'SEO Tytuł', 'en' => 'SEO Title'],
            'meta_description' => ['pl' => 'SEO Opis', 'en' => 'SEO Description'],
            'canonical_url' => 'https://example.com/canonical',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.posts.store'), $data);

        $response->assertRedirect(); // Redirects to edit page
        $this->assertDatabaseHas('posts', [
            'id' => 1,
            'title->pl' => 'Test Post',
            'slug->pl' => 'test-post',
            'meta_title->pl' => 'SEO Tytuł',
            'meta_description->pl' => 'SEO Opis',
            'canonical_url' => 'https://example.com/canonical',
        ]);
    }

    public function test_admin_can_update_post(): void
    {
        $post = Post::factory()->create();

        $data = [
            'title' => ['pl' => 'New Title', 'en' => 'New Title'],
            'slug' => ['pl' => 'new-title', 'en' => 'new-title'],
            'content' => ['pl' => 'New content', 'en' => 'New content'],
            'excerpt' => ['pl' => 'New excerpt', 'en' => 'New excerpt'],
            'status' => 'published',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.posts.update', $post), $data);

        $response->assertRedirect(); // Redirects back or to edit
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title->pl' => 'New Title',
        ]);
    }

    public function test_admin_can_delete_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.posts.destroy', $post));

        $response->assertRedirect();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
