<?php

namespace Tests\Feature;

use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLifecycleCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_lifecycle_dry_run_does_not_modify_rows(): void
    {
        config()->set('media_lifecycle.archive_after_days', 30);
        config()->set('media_lifecycle.retention_days', 60);
        config()->set('media_lifecycle.purge_after_retention_days', 15);

        $oldMedia = Media::create([
            'path' => 'media/old.jpg',
            'mime' => 'image/jpeg',
            'size' => 1024,
            'created_at' => Carbon::now()->subDays(45),
            'updated_at' => Carbon::now()->subDays(45),
        ]);

        $this->artisan('media:lifecycle')
            ->expectsOutputToContain('mode=dry-run')
            ->assertExitCode(0);

        $oldMedia->refresh();
        $this->assertNull($oldMedia->archived_at);
        $this->assertNull($oldMedia->retention_until);
        $this->assertNull($oldMedia->purge_after);
        $this->assertDatabaseHas('media', ['id' => $oldMedia->id]);
    }

    public function test_media_lifecycle_apply_archives_and_purges_due_media(): void
    {
        Storage::fake('public');

        config()->set('media_lifecycle.archive_after_days', 30);
        config()->set('media_lifecycle.retention_days', 60);
        config()->set('media_lifecycle.purge_after_retention_days', 15);

        $archiveCandidate = Media::create([
            'path' => 'media/archive-me.jpg',
            'mime' => 'image/jpeg',
            'size' => 2048,
            'created_at' => Carbon::now()->subDays(40),
            'updated_at' => Carbon::now()->subDays(40),
        ]);

        Storage::disk('public')->put('media/purge-me.jpg', 'binary-content');
        $purgeCandidate = Media::create([
            'path' => 'media/purge-me.jpg',
            'mime' => 'image/jpeg',
            'size' => 512,
            'archived_at' => Carbon::now()->subDays(100),
            'retention_until' => Carbon::now()->subDays(40),
            'purge_after' => Carbon::now()->subDay(),
        ]);

        $this->artisan('media:lifecycle', ['--apply' => true])
            ->expectsOutputToContain('mode=apply')
            ->expectsOutputToContain('Lifecycle apply complete.')
            ->assertExitCode(0);

        $archiveCandidate->refresh();
        $this->assertNotNull($archiveCandidate->archived_at);
        $this->assertNotNull($archiveCandidate->retention_until);
        $this->assertNotNull($archiveCandidate->purge_after);

        $this->assertDatabaseMissing('media', ['id' => $purgeCandidate->id]);
        Storage::disk('public')->assertMissing('media/purge-me.jpg');
    }
}
