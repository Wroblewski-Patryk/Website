<?php

namespace App\Console\Commands;

use App\Services\Operations\OperationalHealthChecker;
use Illuminate\Console\Command;

class CheckOperationalHealth extends Command
{
    protected $signature = 'ops:health-check {--json : Output detailed health payload as JSON}';

    protected $description = 'Check operational readiness of DB, cache, and queue subsystems';

    public function handle(OperationalHealthChecker $healthChecker): int
    {
        $payload = $healthChecker->check();

        if ($this->option('json')) {
            $this->line((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->line($payload['ok'] ? 'Operational health checks passed.' : 'Operational health checks failed.');
        }

        return $payload['ok'] ? self::SUCCESS : self::FAILURE;
    }
}
