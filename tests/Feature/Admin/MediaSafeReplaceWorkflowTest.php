<?php

namespace Tests\Feature\Admin;

use App\Models\Media;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaSafeReplaceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_safe_replace_switches_references_and_can_remove_source_asset(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('media/original.jpg', 'same-content');
        Storage::disk('public')->put('media/duplicate.jpg', 'same-content');

        $checksum = hash('sha256', 'same-content');

        $target = Media::create([
            'path' => 'media/original.jpg',
            'mime' => 'image/jpeg',
            'size' => 1234,
            'checksum' => $checksum,
        ]);

        $source = Media::create([
            'path' => 'media/duplicate.jpg',
            'mime' => 'image/jpeg',
            'size' => 1234,
            'checksum' => $checksum,
        ]);

        $page = Page::factory()->create([
            'content' => [[
                'id' => 'img-1',
                'type' => 'image',
                'content' => [
                    'src' => 'media/duplicate.jpg',
                    'publicSrc' => '/storage/media/duplicate.jpg',
                    'absoluteSrc' => asset('storage/media/duplicate.jpg'),
                ],
            ]],
        ]);

        $this->actingAs($this->admin)->postJson(route('admin.media.safe-replace', $source), [
            'target_media_id' => $target->id,
            'delete_source' => true,
        ])->assertOk();

        $page->refresh();

        $this->assertSame('media/original.jpg', data_get($page->content, '0.content.src'));
        $this->assertSame('/storage/media/original.jpg', data_get($page->content, '0.content.publicSrc'));
        $this->assertSame(asset('storage/media/original.jpg'), data_get($page->content, '0.content.absoluteSrc'));

        $this->assertDatabaseHas('media', ['id' => $target->id]);
        $this->assertDatabaseMissing('media', ['id' => $source->id]);
        $this->assertFalse(Storage::disk('public')->exists('media/duplicate.jpg'));
    }

    public function test_safe_replace_rejects_non_matching_checksums(): void
    {
        $target = Media::create([
            'path' => 'media/a.jpg',
            'mime' => 'image/jpeg',
            'size' => 1234,
            'checksum' => hash('sha256', 'A'),
        ]);

        $source = Media::create([
            'path' => 'media/b.jpg',
            'mime' => 'image/jpeg',
            'size' => 1234,
            'checksum' => hash('sha256', 'B'),
        ]);

        $this->actingAs($this->admin)->postJson(route('admin.media.safe-replace', $source), [
            'target_media_id' => $target->id,
            'delete_source' => true,
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('media', ['id' => $source->id]);
    }
}
