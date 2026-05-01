<?php

namespace App\Console\Commands;

use App\Services\SystemUpdates\UpdateManager;
use Illuminate\Console\Command;

class ApplyApplicationUpdate extends Command
{
    protected $signature = 'updates:apply {--force : Bypass the admin auto-update preference for an operator-triggered apply attempt}';

    protected $description = 'Run the configured Featherly update driver apply contract';

    public function handle(UpdateManager $updateManager): int
    {
        $status = $updateManager->applyUpdate(force: (bool) $this->option('force'));
        $message = $status['operator_message']
            ?? $status['failure_message']
            ?? $status['status_label']
            ?? 'Update apply attempt completed.';

        $this->line($message);

        foreach (($status['operator_instructions'] ?? []) as $instruction) {
            $this->line('- ' . $instruction);
        }

        return self::SUCCESS;
    }
}
