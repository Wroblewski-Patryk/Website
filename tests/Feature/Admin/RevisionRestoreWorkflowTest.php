<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\Post;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RevisionRestoreWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_page_revision_restore_replaces_content_and_keeps_backup_snapshot(): void
    {
        $page = Page::factory()->create([
            'content' => [['id' => 'curr-1', 'type' => 'paragraph', 'content' => ['text' => 'Current page content']]],
        ]);

        $revision = $page->revisions()->create([
            'content' => [['id' => 'rev-1', 'type' => 'paragraph', 'content' => ['text' => 'Restored page content']]],
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.pages.revisions.restore', [
            'page' => $page->id,
            'revision' => $revision->id,
        ]));

        $response->assertRedirect();

        $page->refresh();

        $this->assertSame('Restored page content', data_get($page->content, '0.content.text'));
        $this->assertCount(2, $page->revisions);
    }

    public function test_post_revision_restore_replaces_content_and_keeps_backup_snapshot(): void
    {
        $post = Post::factory()->create([
            'content' => [['id' => 'curr-1', 'type' => 'paragraph', 'content' => ['text' => 'Current post content']]],
        ]);

        $revision = $post->revisions()->create([
            'content' => [['id' => 'rev-1', 'type' => 'paragraph', 'content' => ['text' => 'Restored post content']]],
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.posts.revisions.restore', [
            'post' => $post->id,
            'revision' => $revision->id,
        ]));

        $response->assertRedirect();

        $post->refresh();

        $this->assertSame('Restored post content', data_get($post->content, '0.content.text'));
        $this->assertCount(2, $post->revisions);
    }

    public function test_template_revision_restore_replaces_content_and_keeps_backup_snapshot(): void
    {
        $template = Template::factory()->create([
            'content' => [['id' => 'curr-1', 'type' => 'paragraph', 'content' => ['text' => 'Current template content']]],
        ]);

        $revision = $template->revisions()->create([
            'content' => [['id' => 'rev-1', 'type' => 'paragraph', 'content' => ['text' => 'Restored template content']]],
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.templates.revisions.restore', [
            'template' => $template->id,
            'revision' => $revision->id,
        ]));

        $response->assertRedirect();

        $template->refresh();

        $this->assertSame('Restored template content', data_get($template->content, '0.content.text'));
        $this->assertCount(2, $template->revisions);
    }
}
