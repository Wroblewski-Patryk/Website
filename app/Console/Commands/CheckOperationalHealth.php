<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CheckOperationalHealth extends Command
{
    protected $signature = 'ops:health-check {--json : Output detailed health payload as JSON}';

    protected $description = 'Check operational readiness of DB, cache, and queue subsystems';

    public function handle(): int
    {
        $checks = [
            'database' => $this->databaseCheck(),
            'cache' => $this->cacheCheck(),
            'queue' => $this->queueCheck(),
        ];

        $allHealthy = collect($checks)->every(fn (array $item) => (bool) ($item['ok'] ?? false));

        $payload = [
            'checked_at' => Carbon::now()->toIso8601String(),
            'ok' => $allHealthy,
            'checks' => $checks,
        ];

        if ($this->option('json')) {
            $this->line((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->line($allHealthy ? 'Operational health checks passed.' : 'Operational health checks failed.');
        }

        return $allHealthy ? self::SUCCESS : self::FAILURE;
    }

    private function databaseCheck(): array
    {
        try {
            DB::select('select 1 as ready');
            return ['ok' => true, 'details' => 'Database query succeeded.'];
        } catch (Throwable $exception) {
            return ['ok' => false, 'details' => $exception->getMessage()];
        }
    }

    private function cacheCheck(): array
    {
        $key = 'ops:health-check:cache';
        $value = (string) Carbon::now()->timestamp;

        try {
            Cache::put($key, $value, now()->addMinutes(1));
            $hit = Cache::get($key) === $value;
            Cache::forget($key);

            return [
                'ok' => $hit,
                'details' => $hit ? 'Cache read/write probe succeeded.' : 'Cache probe miss detected.',
            ];
        } catch (Throwable $exception) {
            return ['ok' => false, 'details' => $exception->getMessage()];
        }
    }

    private function queueCheck(): array
    {
        $driver = (string) config('queue.default', 'sync');

        try {
            if ($driver === 'database') {
                $jobsReady = Schema::hasTable('jobs');
                return [
                    'ok' => $jobsReady,
                    'details' => $jobsReady ? 'Database queue table is available.' : 'Missing database queue table: jobs.',
                ];
            }

            $connectionConfig = config("queue.connections.{$driver}");
            if (!is_array($connectionConfig) || $connectionConfig === []) {
                return ['ok' => false, 'details' => "Queue driver [{$driver}] is not configured."];
            }

            return ['ok' => true, 'details' => "Queue driver [{$driver}] configuration loaded."];
        } catch (Throwable $exception) {
            return ['ok' => false, 'details' => $exception->getMessage()];
        }
    }
}
