<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallDetectionMiddlewareTest extends TestCase
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

    public function test_uninstalled_state_redirects_non_installer_routes_to_installer_page(): void
    {
        $response = $this->get(route('auth.login'));

        $response->assertRedirect(route('install.index', ['locale' => app()->getLocale()]));
    }

    public function test_installer_route_is_accessible_when_application_is_uninstalled(): void
    {
        $response = $this->get(route('install.index'));

        $response->assertStatus(200);
        $response->assertSee('Welcome to Featherly Installer');
    }

    public function test_lock_file_marks_application_as_installed(): void
    {
        file_put_contents($this->lockFile, 'installed');

        $response = $this->get(route('auth.login'));

        $response->assertStatus(200);
    }

    public function test_installer_routes_are_blocked_after_installation(): void
    {
        file_put_contents($this->lockFile, 'installed');

        $response = $this->get(route('install.index'));

        $response->assertRedirect(route('auth.login', ['locale' => app()->getLocale()]));
    }
}
