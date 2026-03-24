<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class PerformanceSmokeCommand extends Command
{
    protected $signature = 'perf:smoke
        {--path=* : Request path(s) to check, e.g. /en /en/blog}
        {--threshold= : Slow route threshold in milliseconds}
        {--fail-on-slow : Exit with non-zero code when threshold is exceeded}';

    protected $description = 'Run a lightweight route performance smoke check';

    public function handle(HttpKernel $kernel): int
    {
        $thresholdMs = (int) ($this->option('threshold') ?? config('performance.perf_smoke.threshold_ms', 1200));
        $thresholdMs = max(1, $thresholdMs);

        $paths = $this->option('path');
        if (empty($paths)) {
            $paths = config('performance.perf_smoke.default_paths', ['/en']);
        }

        $rows = [];
        $slowCount = 0;
        $errorCount = 0;

        foreach ($paths as $path) {
            $normalizedPath = $this->normalizePath((string) $path);
            $request = Request::create($normalizedPath, 'GET');

            $start = hrtime(true);
            $response = $kernel->handle($request);
            $elapsedMs = (hrtime(true) - $start) / 1_000_000;
            $kernel->terminate($request, $response);

            $statusCode = $response->getStatusCode();
            $slow = $elapsedMs > $thresholdMs;
            $serverError = $statusCode >= 500;

            if ($slow) {
                $slowCount++;
            }

            if ($serverError) {
                $errorCount++;
            }

            $rows[] = [
                $normalizedPath,
                $statusCode,
                number_format($elapsedMs, 2, '.', ''),
                $slow ? 'yes' : 'no',
                $serverError ? 'yes' : 'no',
            ];
        }

        $this->table(['Path', 'Status', 'Time (ms)', 'Slow', 'Server Error'], $rows);
        $this->line("Threshold: {$thresholdMs} ms");
        $this->line("Slow routes: {$slowCount}");
        $this->line("Server errors: {$errorCount}");

        if ($errorCount > 0) {
            $this->error('Performance smoke failed: server errors detected.');
            return SymfonyCommand::FAILURE;
        }

        if ($slowCount > 0 && (bool) $this->option('fail-on-slow')) {
            $this->error('Performance smoke failed: slow routes detected.');
            return SymfonyCommand::FAILURE;
        }

        $this->info('Performance smoke completed.');
        return SymfonyCommand::SUCCESS;
    }

    private function normalizePath(string $path): string
    {
        $trimmed = trim($path);

        if ($trimmed === '') {
            return '/';
        }

        return str_starts_with($trimmed, '/') ? $trimmed : '/' . $trimmed;
    }
}
