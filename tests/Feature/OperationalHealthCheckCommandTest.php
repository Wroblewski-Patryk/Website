<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OperationalHealthCheckCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_ops_health_check_reports_db_cache_and_queue_readiness(): void
    {
        $exitCode = Artisan::call('ops:health-check', ['--json' => true]);
        $output = Artisan::output();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('"ok": true', $output);
        $this->assertStringContainsString('"database"', $output);
        $this->assertStringContainsString('"cache"', $output);
        $this->assertStringContainsString('"queue"', $output);
    }
}
