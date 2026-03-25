<?php

namespace App\Console\Commands;

use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class EnforceMediaLifecycle extends Command
{
    protected $signature = 'media:lifecycle {--apply : Apply changes instead of dry-run output only} {--chunk=200 : Processing chunk size}';

    protected $description = 'Apply media archive/retention/purge lifecycle policy';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $chunkSize = max(50, (int) $this->option('chunk'));
        $now = Carbon::now();

        $archiveAfterDays = max(1, (int) config('media_lifecycle.archive_after_days', 180));
        $retentionDays = max(1, (int) config('media_lifecycle.retention_days', 180));
        $purgeAfterRetentionDays = max(1, (int) config('media_lifecycle.purge_after_retention_days', 30));

        $archiveCutoff = $now->copy()->subDays($archiveAfterDays);
        $toArchiveQuery = Media::query()
            ->whereNull('archived_at')
            ->where('created_at', '<=', $archiveCutoff);

        $toPurgeQuery = Media::query()
            ->whereNotNull('purge_after')
            ->where('purge_after', '<=', $now);

        $toArchiveCount = (clone $toArchiveQuery)->count();
        $toPurgeCount = (clone $toPurgeQuery)->count();

        $this->info(sprintf(
            '[media:lifecycle] mode=%s archive_candidates=%d purge_candidates=%d',
            $apply ? 'apply' : 'dry-run',
            $toArchiveCount,
            $toPurgeCount
        ));

        if (!$apply) {
            $this->info('Dry-run complete. Re-run with --apply to persist changes.');
            return self::SUCCESS;
        }

        $archived = 0;
        $toArchiveQuery->orderBy('id')->chunkById($chunkSize, function ($records) use (&$archived, $now, $retentionDays, $purgeAfterRetentionDays) {
            foreach ($records as $media) {
                $archivedAt = $now->copy();
                $retentionUntil = $archivedAt->copy()->addDays($retentionDays);
                $purgeAfter = $retentionUntil->copy()->addDays($purgeAfterRetentionDays);

                $media->forceFill([
                    'archived_at' => $archivedAt,
                    'retention_until' => $retentionUntil,
                    'purge_after' => $purgeAfter,
                ])->save();

                $archived++;
            }
        });

        $purged = 0;
        $toPurgeQuery->orderBy('id')->chunkById($chunkSize, function ($records) use (&$purged) {
            foreach ($records as $media) {
                if ($media->path && Storage::disk('public')->exists($media->path)) {
                    Storage::disk('public')->delete($media->path);
                }

                $media->delete();
                $purged++;
            }
        });

        $this->info(sprintf(
            'Lifecycle apply complete. archived=%d purged=%d',
            $archived,
            $purged
        ));

        return self::SUCCESS;
    }
}
