<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\SystemUpdates\UpdateManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use ZipArchive;

class SystemUpdateCheckCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_check_stores_available_release_status(): void
    {
        config()->set('updates.manifest_url', 'https://updates.example.test/manifest.json');
        config()->set('updates.current_version', '1.0.0');

        Http::fake([
            'https://updates.example.test/manifest.json' => Http::response([
                'channels' => [
                    'stable' => [
                        'latest_version' => '1.1.0',
                        'minimum_php_version' => '8.2.0',
                        'release_notes_url' => 'https://example.test/releases/1.1.0',
                        'release_archive_url' => 'https://example.test/releases/featherly-1.1.0.zip',
                        'release_archive_sha256' => str_repeat('a', 64),
                        'manual_review_required' => false,
                    ],
                ],
            ]),
        ]);

        $this->artisan('updates:check')
            ->expectsOutputToContain('Update check complete.')
            ->assertSuccessful();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('1.0.0', $status['current_version']);
        $this->assertSame('1.1.0', $status['latest_version']);
        $this->assertSame('https://example.test/releases/featherly-1.1.0.zip', $status['release_archive_url']);
        $this->assertSame(str_repeat('a', 64), $status['release_archive_sha256']);
        $this->assertTrue($status['update_available']);
        $this->assertSame('available', $status['status']);
        $this->assertTrue($status['php_requirement_ok']);
    }

    public function test_updates_check_fails_closed_when_manifest_is_invalid(): void
    {
        config()->set('updates.manifest_url', 'https://updates.example.test/manifest.json');
        config()->set('updates.current_version', '1.0.0');

        Http::fake([
            'https://updates.example.test/manifest.json' => Http::response([
                'channels' => [
                    'stable' => [],
                ],
            ]),
        ]);

        $this->artisan('updates:check')
            ->expectsOutputToContain('Update check failed:')
            ->assertSuccessful();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertFalse($status['update_available']);
        $this->assertSame('failed', $status['status']);
        $this->assertStringContainsString('latest_version', $status['failure_message']);
    }

    public function test_scheduled_update_check_respects_disabled_setting(): void
    {
        config()->set('updates.current_version', '1.0.0');
        Setting::updateOrCreate(['key' => 'update_checks_enabled'], ['value' => false]);

        Http::fake();

        $this->artisan('updates:check')
            ->expectsOutputToContain('Checks disabled')
            ->assertSuccessful();

        Http::assertNothingSent();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('disabled', $status['status']);
        $this->assertFalse($status['update_available']);
    }

    public function test_updates_apply_uses_fake_driver_when_explicitly_enabled(): void
    {
        config()->set('updates.current_version', '1.0.0');
        config()->set('updates.enable_fake_driver', true);

        Setting::updateOrCreate(['key' => 'auto_update_enabled'], ['value' => true]);
        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'fake']);
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'update_available' => true,
            'manual_review_required' => false,
            'php_requirement_ok' => true,
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        $this->artisan('updates:apply')
            ->expectsOutputToContain('Fake update application completed.')
            ->assertSuccessful();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('1.1.0', $status['current_version']);
        $this->assertFalse($status['update_available']);
        $this->assertSame('applied', $status['apply_status']);
        $this->assertSame('current', $status['status']);
    }

    public function test_updates_apply_blocks_high_risk_manual_review_release(): void
    {
        Setting::updateOrCreate(['key' => 'auto_update_enabled'], ['value' => true]);
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'update_available' => true,
            'manual_review_required' => true,
            'php_requirement_ok' => true,
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        $this->artisan('updates:apply')
            ->expectsOutputToContain('requires manual review')
            ->assertSuccessful();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('blocked', $status['apply_status']);
        $this->assertTrue($status['update_available']);
    }

    public function test_coolify_driver_preflight_reports_config_without_exposing_secret_url(): void
    {
        config()->set('updates.drivers.coolify.deploy_webhook_url', 'https://secret.example.test/deploy?token=super-secret');

        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'coolify']);

        $status = app(UpdateManager::class)->getStatus();

        $this->assertSame('coolify', $status['effective_driver']);
        $this->assertTrue($status['driver_configured']);
        $this->assertTrue($status['driver_supported']);
        $this->assertTrue($status['driver_preflight_ok']);
        $this->assertFalse($status['driver_supports_apply']);
        $this->assertStringNotContainsString('super-secret', $status['driver_preflight_message']);
        $this->assertFalse($status['auto_apply_allowed']);
    }

    public function test_coolify_apply_triggers_configured_webhook_when_explicitly_enabled(): void
    {
        config()->set('updates.drivers.coolify.deploy_webhook_url', 'https://secret.example.test/deploy?token=super-secret');
        config()->set('updates.drivers.coolify.apply_enabled', true);

        Setting::updateOrCreate(['key' => 'auto_update_enabled'], ['value' => true]);
        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'coolify']);
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'update_available' => true,
            'manual_review_required' => false,
            'php_requirement_ok' => true,
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        Http::fake([
            'https://secret.example.test/*' => Http::response(['ok' => true], 202),
        ]);

        $this->artisan('updates:apply')
            ->expectsOutputToContain('Coolify deployment was triggered.')
            ->assertSuccessful();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://secret.example.test/deploy?token=super-secret'
                && $request->method() === 'POST'
                && $request['target_version'] === '1.1.0';
        });

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('deployment_triggered', $status['apply_status']);
        $this->assertSame('coolify', $status['effective_driver']);
        $this->assertTrue($status['update_available']);
        $this->assertSame('1.0.0', $status['current_version']);
        $this->assertStringNotContainsString('super-secret', $status['operator_message']);
    }

    public function test_coolify_apply_stays_disabled_without_environment_kill_switch(): void
    {
        config()->set('updates.drivers.coolify.deploy_webhook_url', 'https://secret.example.test/deploy?token=super-secret');
        config()->set('updates.drivers.coolify.apply_enabled', false);

        Setting::updateOrCreate(['key' => 'auto_update_enabled'], ['value' => true]);
        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'coolify']);
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'update_available' => true,
            'manual_review_required' => false,
            'php_requirement_ok' => true,
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        Http::fake();

        $this->artisan('updates:apply')
            ->expectsOutputToContain('Coolify apply trigger is disabled')
            ->assertSuccessful();

        Http::assertNothingSent();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('preflight_only', $status['apply_status']);
        $this->assertStringNotContainsString('super-secret', $status['operator_message']);
    }

    public function test_updates_confirm_marks_triggered_deployment_current_when_runtime_version_matches(): void
    {
        config()->set('updates.current_version', '1.1.0');

        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'update_available' => true,
            'apply_status' => 'deployment_triggered',
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        $this->artisan('updates:confirm')
            ->expectsOutputToContain('Deployment confirmed at the expected application version.')
            ->assertSuccessful();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('1.1.0', $status['current_version']);
        $this->assertFalse($status['update_available']);
        $this->assertSame('confirmed', $status['apply_status']);
        $this->assertSame('current', $status['status']);
        $this->assertNotEmpty($status['confirmed_at']);
        $this->assertSame('passed', $status['health_status']);
        $this->assertNotEmpty($status['health_checks']);
    }

    public function test_updates_confirm_keeps_triggered_deployment_open_until_runtime_version_matches(): void
    {
        config()->set('updates.current_version', '1.0.0');

        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'update_available' => true,
            'apply_status' => 'deployment_triggered',
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        $this->artisan('updates:confirm')
            ->expectsOutputToContain('Deployment is still awaiting version confirmation.')
            ->assertSuccessful();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('1.0.0', $status['current_version']);
        $this->assertTrue($status['update_available']);
        $this->assertSame('awaiting_confirmation', $status['apply_status']);
        $this->assertSame('available', $status['status']);
    }

    public function test_updates_confirm_does_not_complete_when_operational_health_fails(): void
    {
        config()->set('updates.current_version', '1.1.0');
        config()->set('queue.default', 'missing-driver');

        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'update_available' => true,
            'apply_status' => 'deployment_triggered',
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        $this->artisan('updates:confirm')
            ->expectsOutputToContain('Deployment version matches, but operational health checks failed.')
            ->assertSuccessful();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('1.1.0', $status['current_version']);
        $this->assertTrue($status['update_available']);
        $this->assertSame('confirmation_health_failed', $status['apply_status']);
        $this->assertSame('failed', $status['health_status']);
        $this->assertSame('failed', $status['status']);
        $this->assertArrayHasKey('queue', $status['health_checks']);
    }

    public function test_archive_driver_preflight_fails_closed_without_paths(): void
    {
        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'archive']);

        $status = app(UpdateManager::class)->getStatus();

        $this->assertSame('archive', $status['effective_driver']);
        $this->assertFalse($status['driver_configured']);
        $this->assertFalse($status['driver_supported']);
        $this->assertFalse($status['driver_preflight_ok']);
        $this->assertStringContainsString('FEATHERLY_UPDATE_ARCHIVE_STAGING_PATH', $status['driver_preflight_message']);
        $this->assertFalse($status['auto_apply_allowed']);
    }

    public function test_archive_driver_preflight_requires_release_integrity_metadata(): void
    {
        $basePath = storage_path('framework/testing/archive-updates');
        $stagingPath = $basePath . '/staging/current';
        $releasePath = $basePath . '/releases/current';

        if (!is_dir(dirname($stagingPath))) {
            mkdir(dirname($stagingPath), 0777, true);
        }

        if (!is_dir(dirname($releasePath))) {
            mkdir(dirname($releasePath), 0777, true);
        }

        config()->set('updates.drivers.archive.staging_path', $stagingPath);
        config()->set('updates.drivers.archive.release_path', $releasePath);

        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'archive']);
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'update_available' => true,
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        $status = app(UpdateManager::class)->getStatus();

        $this->assertSame('archive', $status['effective_driver']);
        $this->assertTrue($status['driver_configured']);
        $this->assertTrue($status['driver_supported']);
        $this->assertFalse($status['driver_preflight_ok']);
        $this->assertStringContainsString('release_archive_url', $status['driver_preflight_message']);
        $this->assertFalse($status['auto_apply_allowed']);
    }

    public function test_archive_driver_preflight_accepts_valid_release_integrity_metadata_but_stays_preflight_only(): void
    {
        $basePath = storage_path('framework/testing/archive-updates');
        $stagingPath = $basePath . '/staging/current';
        $releasePath = $basePath . '/releases/current';

        if (!is_dir(dirname($stagingPath))) {
            mkdir(dirname($stagingPath), 0777, true);
        }

        if (!is_dir(dirname($releasePath))) {
            mkdir(dirname($releasePath), 0777, true);
        }

        config()->set('updates.drivers.archive.staging_path', $stagingPath);
        config()->set('updates.drivers.archive.release_path', $releasePath);

        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'archive']);
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'release_archive_url' => 'https://example.test/releases/featherly-1.1.0.zip',
            'release_archive_sha256' => str_repeat('b', 64),
            'update_available' => true,
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        $status = app(UpdateManager::class)->getStatus();

        $this->assertSame('archive', $status['effective_driver']);
        $this->assertTrue($status['driver_configured']);
        $this->assertTrue($status['driver_supported']);
        $this->assertTrue($status['driver_preflight_ok']);
        $this->assertFalse($status['driver_supports_apply']);
        $this->assertFalse($status['auto_apply_allowed']);
    }

    public function test_archive_apply_downloads_and_verifies_archive_without_switching_files(): void
    {
        $archiveBody = class_exists(ZipArchive::class)
            ? $this->buildReleaseArchive([
                'artisan' => '#!/usr/bin/env php',
                'composer.json' => '{"name":"featherly/release"}',
                'bootstrap/app.php' => '<?php return true;',
                'public/index.php' => '<?php echo "ok";',
            ])
            : 'verified archive bytes';
        $archiveSha256 = hash('sha256', $archiveBody);
        $basePath = storage_path('framework/testing/archive-apply-success');
        $stagingPath = $basePath . '/staging/current';
        $releasePath = $basePath . '/releases/current';

        if (!is_dir(dirname($stagingPath))) {
            mkdir(dirname($stagingPath), 0777, true);
        }

        if (!is_dir(dirname($releasePath))) {
            mkdir(dirname($releasePath), 0777, true);
        }

        config()->set('updates.drivers.archive.staging_path', $stagingPath);
        config()->set('updates.drivers.archive.release_path', $releasePath);

        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'archive']);
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'release_archive_url' => 'https://example.test/releases/featherly-1.1.0.zip',
            'release_archive_sha256' => $archiveSha256,
            'update_available' => true,
            'manual_review_required' => false,
            'php_requirement_ok' => true,
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        Http::fake([
            'https://example.test/releases/featherly-1.1.0.zip' => Http::response($archiveBody, 200),
        ]);

        $this->artisan('updates:apply --force')
            ->expectsOutputToContain('Release archive downloaded')
            ->assertSuccessful();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertContains($status['apply_status'], ['archive_verified', 'archive_staged']);
        $this->assertSame($archiveSha256, $status['archive_verified_sha256']);
        $this->assertSame(strlen($archiveBody), $status['archive_verified_bytes']);
        $this->assertSame('featherly-1.1.0.zip', $status['archive_verified_filename']);
        $this->assertTrue(file_exists($stagingPath . '/featherly-1.1.0.zip'));
        $this->assertContains($status['archive_extraction_status'], ['pending', 'unavailable', 'validated']);
        $this->assertTrue($status['update_available']);
        $this->assertSame('1.0.0', $status['current_version']);
    }

    public function test_archive_apply_removes_staged_archive_when_checksum_fails(): void
    {
        $archiveBody = 'tampered archive bytes';
        $basePath = storage_path('framework/testing/archive-apply-failure');
        $stagingPath = $basePath . '/staging/current';
        $releasePath = $basePath . '/releases/current';

        if (!is_dir(dirname($stagingPath))) {
            mkdir(dirname($stagingPath), 0777, true);
        }

        if (!is_dir(dirname($releasePath))) {
            mkdir(dirname($releasePath), 0777, true);
        }

        config()->set('updates.drivers.archive.staging_path', $stagingPath);
        config()->set('updates.drivers.archive.release_path', $releasePath);

        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'archive']);
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'release_archive_url' => 'https://example.test/releases/featherly-1.1.0.zip',
            'release_archive_sha256' => str_repeat('c', 64),
            'update_available' => true,
            'manual_review_required' => false,
            'php_requirement_ok' => true,
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        Http::fake([
            'https://example.test/releases/featherly-1.1.0.zip' => Http::response($archiveBody, 200),
        ]);

        $this->artisan('updates:apply --force')
            ->expectsOutputToContain('Release archive SHA-256 verification failed.')
            ->assertSuccessful();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('archive_verification_failed', $status['apply_status']);
        $this->assertFalse(file_exists($stagingPath . '/featherly-1.1.0.zip'));
        $this->assertTrue($status['update_available']);
        $this->assertSame('1.0.0', $status['current_version']);
    }

    public function test_archive_apply_extracts_verified_archive_to_staging_without_switching_files(): void
    {
        if (!class_exists(ZipArchive::class)) {
            $this->markTestSkipped('ZipArchive extension is required for archive extraction validation.');
        }

        $archiveBody = $this->buildReleaseArchive([
            'artisan' => '#!/usr/bin/env php',
            'composer.json' => '{"name":"featherly/release"}',
            'bootstrap/app.php' => '<?php return true;',
            'public/index.php' => '<?php echo "ok";',
        ]);
        $archiveSha256 = hash('sha256', $archiveBody);
        $basePath = storage_path('framework/testing/archive-extract-success');
        $stagingPath = $basePath . '/staging/current';
        $releasePath = $basePath . '/releases/current';

        $this->prepareArchivePaths($stagingPath, $releasePath);

        config()->set('updates.drivers.archive.staging_path', $stagingPath);
        config()->set('updates.drivers.archive.release_path', $releasePath);

        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'archive']);
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'release_archive_url' => 'https://example.test/releases/featherly-1.1.0.zip',
            'release_archive_sha256' => $archiveSha256,
            'update_available' => true,
            'manual_review_required' => false,
            'php_requirement_ok' => true,
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        Http::fake([
            'https://example.test/releases/featherly-1.1.0.zip' => Http::response($archiveBody, 200),
        ]);

        $this->artisan('updates:apply --force')
            ->expectsOutputToContain('Release archive downloaded, verified, and extracted to staging.')
            ->assertSuccessful();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('archive_staged', $status['apply_status']);
        $this->assertSame('validated', $status['archive_extraction_status']);
        $this->assertSame(4, $status['archive_extracted_file_count']);
        $this->assertTrue(is_file($status['archive_extracted_directory'] . '/artisan'));
        $this->assertTrue($status['update_available']);
        $this->assertSame('1.0.0', $status['current_version']);
    }

    public function test_archive_apply_rejects_extracted_archive_missing_required_files(): void
    {
        if (!class_exists(ZipArchive::class)) {
            $this->markTestSkipped('ZipArchive extension is required for archive extraction validation.');
        }

        $archiveBody = $this->buildReleaseArchive([
            'artisan' => '#!/usr/bin/env php',
            'composer.json' => '{"name":"featherly/release"}',
        ]);
        $archiveSha256 = hash('sha256', $archiveBody);
        $basePath = storage_path('framework/testing/archive-extract-missing');
        $stagingPath = $basePath . '/staging/current';
        $releasePath = $basePath . '/releases/current';

        $this->prepareArchivePaths($stagingPath, $releasePath);

        config()->set('updates.drivers.archive.staging_path', $stagingPath);
        config()->set('updates.drivers.archive.release_path', $releasePath);

        Setting::updateOrCreate(['key' => 'preferred_update_driver'], ['value' => 'archive']);
        Setting::updateOrCreate(['key' => 'system_update_status'], ['value' => [
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'release_archive_url' => 'https://example.test/releases/featherly-1.1.0.zip',
            'release_archive_sha256' => $archiveSha256,
            'update_available' => true,
            'manual_review_required' => false,
            'php_requirement_ok' => true,
            'status' => 'available',
            'status_label' => 'Update available',
        ]]);

        Http::fake([
            'https://example.test/releases/featherly-1.1.0.zip' => Http::response($archiveBody, 200),
        ]);

        $this->artisan('updates:apply --force')
            ->expectsOutputToContain('Release archive staging validation failed.')
            ->assertSuccessful();

        $status = Setting::query()->where('key', 'system_update_status')->firstOrFail()->value;

        $this->assertSame('archive_staging_failed', $status['apply_status']);
        $this->assertSame('failed', $status['archive_extraction_status']);
        $this->assertFalse(is_dir($stagingPath . '/extracted/1.1.0'));
        $this->assertTrue($status['update_available']);
        $this->assertSame('1.0.0', $status['current_version']);
    }

    /**
     * @param  array<string, string>  $files
     */
    private function buildReleaseArchive(array $files): string
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'featherly-release-');
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::OVERWRITE);

        foreach ($files as $path => $contents) {
            $zip->addFromString($path, $contents);
        }

        $zip->close();
        $contents = file_get_contents($zipPath);
        @unlink($zipPath);

        return is_string($contents) ? $contents : '';
    }

    private function prepareArchivePaths(string $stagingPath, string $releasePath): void
    {
        if (!is_dir(dirname($stagingPath))) {
            mkdir(dirname($stagingPath), 0777, true);
        }

        if (!is_dir(dirname($releasePath))) {
            mkdir(dirname($releasePath), 0777, true);
        }
    }
}
