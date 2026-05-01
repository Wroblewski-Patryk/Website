<?php

namespace App\Console\Commands;

use App\Services\SystemUpdates\UpdateManager;
use Illuminate\Console\Command;

class ConfirmApplicationUpdate extends Command
{
    protected $signature = 'updates:confirm';

    protected $description = 'Confirm that the running Featherly application version matches the last triggered update';

    public function handle(UpdateManager $updateManager): int
    {
        $status = $updateManager->confirmDeployment();

        $this->line($status['operator_message'] ?? $status['status_label'] ?? 'Update confirmation completed.');

        return self::SUCCESS;
    }
}
