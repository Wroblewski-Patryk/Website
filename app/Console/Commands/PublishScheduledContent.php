<?php

namespace App\Console\Commands;

use App\Models\Form;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Throwable;

class PublishScheduledContent extends Command
{
    protected $signature = 'publish:scheduled';

    protected $description = 'Publish planned content whose published_at date has passed';

    public function handle(): int
    {
        $startedAt = Carbon::now();
        $startedNs = hrtime(true);
        $totalPublished = 0;
        $byModel = [];
        $now = Carbon::now();

        $models = [Page::class, Post::class, Project::class, Form::class];

        try {
            foreach ($models as $modelClass) {
                $query = $modelClass::where('status', 'planned')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', $now);

                $dueCount = (clone $query)->count();
                $publishedCount = $query->update(['status' => 'published']);
                $modelKey = class_basename($modelClass);

                $byModel[$modelKey] = [
                    'due' => $dueCount,
                    'published' => $publishedCount,
                ];

                if ($publishedCount > 0) {
                    $this->info("Published {$publishedCount} {$modelKey}(s).");
                    $totalPublished += $publishedCount;
                }
            }

            if ($totalPublished === 0) {
                $this->info('No scheduled content to publish.');
            }

            $durationMs = (int) round((hrtime(true) - $startedNs) / 1_000_000);
            Log::info('publish:scheduled summary', [
                'scheduled_at' => $startedAt->toIso8601String(),
                'duration_ms' => $durationMs,
                'total_published' => $totalPublished,
                'by_model' => $byModel,
            ]);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $durationMs = (int) round((hrtime(true) - $startedNs) / 1_000_000);
            Log::error('publish:scheduled failed', [
                'scheduled_at' => $startedAt->toIso8601String(),
                'duration_ms' => $durationMs,
                'total_published' => $totalPublished,
                'by_model' => $byModel,
                'error' => $exception->getMessage(),
            ]);

            $this->error('Failed to publish scheduled content.');

            return SymfonyCommand::FAILURE;
        }
    }
}
