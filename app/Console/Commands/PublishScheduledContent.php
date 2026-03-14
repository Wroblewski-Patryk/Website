<?php

namespace App\Console\Commands;

use App\Models\Form;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PublishScheduledContent extends Command
{
    protected $signature = 'publish:scheduled';

    protected $description = 'Publish planned content whose published_at date has passed';

    public function handle(): int
    {
        $now = Carbon::now();
        $total = 0;

        $models = [Page::class, Post::class, Project::class, Form::class];

        foreach ($models as $modelClass) {
            $count = $modelClass::where('status', 'planned')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', $now)
                ->update(['status' => 'published']);

            if ($count > 0) {
                $this->info("Published {$count} " . class_basename($modelClass) . "(s).");
                $total += $count;
            }
        }

        if ($total === 0) {
            $this->info('No scheduled content to publish.');
        }

        return self::SUCCESS;
    }
}
