<?php

namespace App\Console\Commands;

use App\Services\SystemUpdates\UpdateManager;
use Illuminate\Console\Command;

class CheckForApplicationUpdates extends Command
{
    protected $signature = 'updates:check';

    protected $description = 'Check the trusted Featherly release manifest for application updates';

    public function handle(UpdateManager $updateManager): int
    {
        $status = $updateManager->checkForUpdates();

        $message = $status['failure_message']
            ? 'Update check failed: ' . $status['failure_message']
            : sprintf(
                'Update check complete. Current: %s, Latest: %s, Status: %s',
                $status['current_version'] ?? 'unknown',
                $status['latest_version'] ?? 'unknown',
                $status['status_label'] ?? 'unknown'
            );

        $this->line($message);

        return self::SUCCESS;
    }
}
