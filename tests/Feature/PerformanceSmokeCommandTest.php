<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceSmokeCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_performance_smoke_command_runs_with_custom_paths(): void
    {
        $this->artisan('perf:smoke', [
            '--path' => ['/up'],
            '--threshold' => 5000,
        ])
            ->expectsOutput('Performance smoke completed.')
            ->assertExitCode(0);
    }
}
