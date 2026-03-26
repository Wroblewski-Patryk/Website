<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallDatabaseValidationTest extends TestCase
{
    use RefreshDatabase;

    private string $lockFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->lockFile = storage_path('framework/testing-installer.lock');
        @unlink($this->lockFile);

        config([
            'installer.bypass_in_testing' => false,
            'installer.lock_file' => $this->lockFile,
        ]);
    }

    protected function tearDown(): void
    {
        @unlink($this->lockFile);
        parent::tearDown();
    }

    public function test_database_validation_fails_for_unreachable_mysql_credentials(): void
    {
        $response = $this->post(route('install.database.validate'), [
            'connection' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '1',
            'database' => 'featherly',
            'username' => 'root',
            'password' => 'secret',
        ]);

        $response->assertSessionHasErrors(['database']);
    }

    public function test_database_validation_accepts_sqlite_and_stores_settings_in_session(): void
    {
        $sqliteFile = storage_path('framework/testing-installer.sqlite');
        @unlink($sqliteFile);
        touch($sqliteFile);

        $response = $this->post(route('install.database.validate'), [
            'connection' => 'sqlite',
            'sqlite_path' => $sqliteFile,
        ]);

        $response
            ->assertRedirect(route('install.index', ['locale' => app()->getLocale()]))
            ->assertSessionHas('installer.database_validated', true)
            ->assertSessionHas('installer.database.connection', 'sqlite')
            ->assertSessionHas('installer.database.sqlite_path', $sqliteFile);

        @unlink($sqliteFile);
    }

    public function test_language_cannot_be_saved_before_database_validation(): void
    {
        $response = $this->post(route('install.language.store'), [
            'language' => 'pl',
        ]);

        $response->assertSessionHasErrors(['language']);
    }

    public function test_language_can_be_saved_after_database_validation(): void
    {
        $response = $this->withSession([
            'installer.database_validated' => true,
        ])->post(route('install.language.store'), [
            'language' => 'pl',
        ]);

        $response
            ->assertRedirect(route('install.index', ['locale' => app()->getLocale()]))
            ->assertSessionHas('installer.language', 'pl')
            ->assertSessionHas('installer.language_selected', true);
    }
}
