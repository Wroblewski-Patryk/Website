<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class InstallFinalizeTest extends TestCase
{
    use RefreshDatabase;

    private string $lockFile;
    private string $envFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->lockFile = storage_path('framework/testing-installer-finalize.lock');
        $this->envFile = storage_path('framework/testing-installer.env');

        @unlink($this->lockFile);
        @unlink($this->envFile);

        file_put_contents($this->envFile, "APP_LOCALE=\"en\"\nDB_CONNECTION=\"sqlite\"\nDB_DATABASE=\"database/database.sqlite\"\n");

        config([
            'installer.bypass_in_testing' => false,
            'installer.lock_file' => $this->lockFile,
            'installer.env_file' => $this->envFile,
        ]);
    }

    protected function tearDown(): void
    {
        @unlink($this->lockFile);
        @unlink($this->envFile);
        parent::tearDown();
    }

    public function test_finalize_requires_language_selection(): void
    {
        $response = $this->withSession([
            'installer.database_validated' => true,
        ])->post(route('install.finalize'));

        $response->assertSessionHasErrors(['finalize']);
    }

    public function test_finalize_runs_migrations_seeds_and_creates_lock_file(): void
    {
        Artisan::shouldReceive('call')
            ->once()
            ->with('migrate', ['--force' => true])
            ->andReturn(0);

        Artisan::shouldReceive('call')
            ->once()
            ->with('db:seed', ['--force' => true])
            ->andReturn(0);

        $response = $this->withSession([
            'installer.database_validated' => true,
            'installer.language_selected' => true,
            'installer.language' => 'pl',
            'installer.database' => [
                'connection' => 'sqlite',
                'sqlite_path' => database_path('database.sqlite'),
            ],
        ])->post(route('install.finalize'));

        $response->assertRedirect(route('auth.login', ['locale' => app()->getLocale()]));
        $this->assertFileExists($this->lockFile);

        $envContents = (string) file_get_contents($this->envFile);
        $this->assertStringContainsString('APP_LOCALE="pl"', $envContents);
        $this->assertStringContainsString('DB_CONNECTION="sqlite"', $envContents);
    }
}
