<?php

namespace App\Services\SystemUpdates;

use App\Models\Setting;
use App\Services\Operations\OperationalHealthChecker;
use App\Services\SystemUpdates\Drivers\ArchiveUpdateDriver;
use App\Services\SystemUpdates\Drivers\CoolifyUpdateDriver;
use App\Services\SystemUpdates\Drivers\FakeUpdateDriver;
use App\Services\SystemUpdates\Drivers\ManualUpdateDriver;
use App\Support\AuditLogger;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class UpdateManager
{
    private const STATUS_KEY = 'system_update_status';

    public function getSettings(): array
    {
        $stored = Setting::query()->get()->pluck('value', 'key')->all();

        return [
            'update_checks_enabled' => $this->toBool($stored['update_checks_enabled'] ?? true, true),
            'auto_update_enabled' => $this->toBool($stored['auto_update_enabled'] ?? false, false),
            'update_release_channel' => $this->toString($stored['update_release_channel'] ?? config('updates.default_channel', 'stable'), 'stable'),
            'preferred_update_driver' => $this->toString($stored['preferred_update_driver'] ?? 'auto', 'auto'),
        ];
    }

    public function getStatus(): array
    {
        $settings = $this->getSettings();
        $status = $this->normalizeStatus($this->readSetting(self::STATUS_KEY));
        $driver = $this->resolveDriver($settings['preferred_update_driver']);
        $preflight = $driver->preflight($status, $settings);
        $autoApplyEnvEnabled = (bool) config('updates.auto_apply_enabled', true);
        $driverSupportsApply = (bool) ($preflight['supports_apply'] ?? false);

        return [
            ...$status,
            'effective_driver' => $driver->key(),
            'driver_label' => $driver->label(),
            'driver_configured' => $driver->isConfigured(),
            'driver_supported' => $driver->isSupported(),
            'driver_supports_apply' => $driverSupportsApply,
            'driver_preflight_ok' => (bool) ($preflight['ok'] ?? false),
            'driver_preflight_message' => $this->toString($preflight['message'] ?? null, null),
            'driver_options' => $this->driverOptions(),
            'update_checks_enabled' => $settings['update_checks_enabled'],
            'auto_update_enabled' => $settings['auto_update_enabled'],
            'release_channel' => $settings['update_release_channel'],
            'preferred_update_driver' => $settings['preferred_update_driver'],
            'auto_apply_env_enabled' => $autoApplyEnvEnabled,
            'auto_apply_allowed' => $autoApplyEnvEnabled
                && $settings['auto_update_enabled']
                && $driverSupportsApply
                && (bool) ($preflight['ok'] ?? false)
                && ($status['update_available'] ?? false)
                && !($status['manual_review_required'] ?? false)
                && ($status['php_requirement_ok'] ?? true),
        ];
    }

    public function checkForUpdates(bool $force = false): array
    {
        $settings = $this->getSettings();
        $currentVersion = (string) config('updates.current_version', '0.0.0');
        $checkedAt = Carbon::now()->toIso8601String();

        if (!$force && !$settings['update_checks_enabled']) {
            $status = [
                ...$this->normalizeStatus($this->readSetting(self::STATUS_KEY)),
                'current_version' => $currentVersion,
                'checked_at' => $checkedAt,
                'last_attempted_at' => $checkedAt,
                'failure_message' => null,
                'status' => 'disabled',
                'status_label' => 'Checks disabled',
            ];

            $this->writeSetting(self::STATUS_KEY, $status);

            return $this->getStatus();
        }

        try {
            $manifest = $this->fetchManifest($settings['update_release_channel']);
            $latestVersion = $this->toString($manifest['latest_version'] ?? null, '');

            if ($latestVersion === '') {
                throw new RuntimeException('Release manifest is missing latest_version.');
            }

            $minimumPhpVersion = $this->toString($manifest['minimum_php_version'] ?? null, '');
            $phpRequirementOk = $minimumPhpVersion === ''
                || version_compare(PHP_VERSION, $minimumPhpVersion, '>=');
            $manualReviewRequired = (bool) ($manifest['manual_review_required'] ?? false);
            $updateAvailable = version_compare($latestVersion, $currentVersion, '>');

            $status = [
                'current_version' => $currentVersion,
                'latest_version' => $latestVersion,
                'checked_at' => $checkedAt,
                'last_attempted_at' => $checkedAt,
                'update_available' => $updateAvailable,
                'failure_message' => null,
                'release_notes_url' => $this->toString($manifest['release_notes_url'] ?? null, null),
                'release_archive_url' => $this->toString($manifest['release_archive_url'] ?? null, null),
                'release_archive_sha256' => $this->toString($manifest['release_archive_sha256'] ?? null, null),
                'manual_review_required' => $manualReviewRequired,
                'minimum_php_version' => $minimumPhpVersion !== '' ? $minimumPhpVersion : null,
                'php_requirement_ok' => $phpRequirementOk,
                'manifest_source' => $this->toString(config('updates.manifest_url'), null),
                'status' => $updateAvailable ? 'available' : 'current',
                'status_label' => $updateAvailable ? 'Update available' : 'Up to date',
            ];

            $this->writeSetting(self::STATUS_KEY, $status);

            AuditLogger::log('updates.checked', [
                'channel' => $settings['update_release_channel'],
                'forced' => $force,
                'latest_version' => $latestVersion,
                'current_version' => $currentVersion,
                'update_available' => $updateAvailable,
                'manual_review_required' => $manualReviewRequired,
            ]);
        } catch (Throwable $exception) {
            $status = [
                ...$this->normalizeStatus($this->readSetting(self::STATUS_KEY)),
                'current_version' => $currentVersion,
                'checked_at' => $checkedAt,
                'last_attempted_at' => $checkedAt,
                'update_available' => false,
                'failure_message' => $exception->getMessage(),
                'status' => 'failed',
                'status_label' => 'Check failed',
            ];

            $this->writeSetting(self::STATUS_KEY, $status);

            AuditLogger::log('updates.check_failed', [
                'channel' => $settings['update_release_channel'],
                'forced' => $force,
                'current_version' => $currentVersion,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->getStatus();
    }

    public function applyUpdate(bool $force = false): array
    {
        $settings = $this->getSettings();
        $status = $this->normalizeStatus($this->readSetting(self::STATUS_KEY));
        $driver = $this->resolveDriver($settings['preferred_update_driver']);
        $attemptedAt = Carbon::now()->toIso8601String();

        $blockedReason = $this->applicationBlockReason($status, $settings, $force);
        if ($blockedReason !== null) {
            $this->writeApplyStatus($status, [
                'last_attempted_at' => $attemptedAt,
                'apply_status' => 'blocked',
                'failure_message' => $blockedReason,
                'operator_message' => $blockedReason,
                'effective_driver' => $driver->key(),
            ]);

            AuditLogger::log('updates.apply_blocked', [
                'driver' => $driver->key(),
                'message' => $blockedReason,
                'force' => $force,
            ]);

            return $this->getStatus();
        }

        $preflight = $driver->preflight($status, $settings);
        if (!($preflight['ok'] ?? false)) {
            $message = $this->toString($preflight['message'] ?? null, 'Update driver preflight failed.');

            $this->writeApplyStatus($status, [
                'last_attempted_at' => $attemptedAt,
                'apply_status' => 'preflight_failed',
                'failure_message' => $message,
                'operator_message' => $message,
                'effective_driver' => $driver->key(),
            ]);

            AuditLogger::log('updates.apply_preflight_failed', [
                'driver' => $driver->key(),
                'message' => $message,
            ]);

            return $this->getStatus();
        }

        try {
            $result = $driver->apply($status, $settings);
            $applied = (bool) ($result['applied'] ?? false);
            $message = $this->toString($result['message'] ?? null, $applied ? 'Update applied.' : 'Manual update required.');

            $updates = [
                'last_attempted_at' => $attemptedAt,
                'apply_status' => $this->toString($result['apply_status'] ?? null, $applied ? 'applied' : 'manual_required'),
                'failure_message' => null,
                'operator_message' => $message,
                'operator_instructions' => $result['operator_instructions'] ?? [],
                'rollback_note' => $this->toString($result['rollback_note'] ?? null, null),
                'effective_driver' => $driver->key(),
            ];

            foreach ([
                'archive_verified_at',
                'archive_verified_sha256',
                'archive_verified_bytes',
                'archive_verified_filename',
                'archive_extraction_status',
                'archive_extraction_message',
                'archive_extracted_directory',
                'archive_extracted_file_count',
            ] as $archiveField) {
                if (array_key_exists($archiveField, $result)) {
                    $updates[$archiveField] = $result[$archiveField];
                }
            }

            if ($applied && isset($status['latest_version'])) {
                $updates['current_version'] = $status['latest_version'];
                $updates['update_available'] = false;
                $updates['status'] = 'current';
                $updates['status_label'] = 'Up to date';
            }

            $this->writeApplyStatus($status, $updates);

            $applyStatus = $updates['apply_status'] ?? null;
            $event = $applied
                ? 'updates.applied'
                : ($applyStatus === 'deployment_triggered' ? 'updates.deployment_triggered' : 'updates.manual_required');

            AuditLogger::log($event, [
                'driver' => $driver->key(),
                'latest_version' => $status['latest_version'] ?? null,
                'message' => $message,
            ]);
        } catch (Throwable $exception) {
            $this->writeApplyStatus($status, [
                'last_attempted_at' => $attemptedAt,
                'apply_status' => 'failed',
                'failure_message' => $exception->getMessage(),
                'operator_message' => $exception->getMessage(),
                'effective_driver' => $driver->key(),
            ]);

            AuditLogger::log('updates.apply_failed', [
                'driver' => $driver->key(),
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->getStatus();
    }

    public function confirmDeployment(?OperationalHealthChecker $healthChecker = null): array
    {
        $status = $this->normalizeStatus($this->readSetting(self::STATUS_KEY));
        $currentVersion = (string) config('updates.current_version', '0.0.0');
        $latestVersion = $this->toString($status['latest_version'] ?? null, null);
        $confirmedAt = Carbon::now()->toIso8601String();

        if ($latestVersion === null || $latestVersion === '') {
            $message = 'No target update version is recorded for confirmation.';

            $this->writeApplyStatus($status, [
                'current_version' => $currentVersion,
                'last_attempted_at' => $confirmedAt,
                'apply_status' => 'confirmation_unavailable',
                'failure_message' => $message,
                'operator_message' => $message,
            ]);

            AuditLogger::log('updates.confirmation_unavailable', [
                'current_version' => $currentVersion,
            ]);

            return $this->getStatus();
        }

        if (version_compare($currentVersion, $latestVersion, '>=')) {
            $health = ($healthChecker ?? app(OperationalHealthChecker::class))->check();

            if (!($health['ok'] ?? false)) {
                $message = 'Deployment version matches, but operational health checks failed.';

                $this->writeApplyStatus($status, [
                    'current_version' => $currentVersion,
                    'last_attempted_at' => $confirmedAt,
                    'health_checked_at' => $this->toString($health['checked_at'] ?? null, $confirmedAt),
                    'health_status' => 'failed',
                    'health_checks' => $health['checks'] ?? [],
                    'apply_status' => 'confirmation_health_failed',
                    'failure_message' => $message,
                    'operator_message' => $message,
                    'status' => 'failed',
                    'status_label' => 'Health check failed',
                ]);

                AuditLogger::log('updates.confirmation_health_failed', [
                    'current_version' => $currentVersion,
                    'latest_version' => $latestVersion,
                ]);

                return $this->getStatus();
            }

            $message = 'Deployment confirmed at the expected application version.';

            $this->writeApplyStatus($status, [
                'current_version' => $currentVersion,
                'last_attempted_at' => $confirmedAt,
                'confirmed_at' => $confirmedAt,
                'health_checked_at' => $this->toString($health['checked_at'] ?? null, $confirmedAt),
                'health_status' => 'passed',
                'health_checks' => $health['checks'] ?? [],
                'update_available' => false,
                'failure_message' => null,
                'apply_status' => 'confirmed',
                'operator_message' => $message,
                'status' => 'current',
                'status_label' => 'Up to date',
            ]);

            AuditLogger::log('updates.confirmed', [
                'current_version' => $currentVersion,
                'latest_version' => $latestVersion,
            ]);

            return $this->getStatus();
        }

        $message = sprintf(
            'Deployment is still awaiting version confirmation. Current: %s, Expected: %s.',
            $currentVersion,
            $latestVersion
        );

        $this->writeApplyStatus($status, [
            'current_version' => $currentVersion,
            'last_attempted_at' => $confirmedAt,
            'apply_status' => 'awaiting_confirmation',
            'failure_message' => null,
            'operator_message' => $message,
            'status' => 'available',
            'status_label' => 'Update available',
        ]);

        AuditLogger::log('updates.awaiting_confirmation', [
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
        ]);

        return $this->getStatus();
    }

    private function fetchManifest(string $channel): array
    {
        $manifestUrl = $this->toString(config('updates.manifest_url'), '');

        if ($manifestUrl === '') {
            throw new RuntimeException('Update manifest URL is not configured.');
        }

        $response = Http::timeout(5)
            ->acceptJson()
            ->get($manifestUrl);

        if (!$response->successful()) {
            throw new RuntimeException('Update manifest request failed.');
        }

        $payload = $response->json();

        if (!is_array($payload)) {
            throw new RuntimeException('Update manifest payload is invalid.');
        }

        $channels = Arr::get($payload, 'channels');
        if (is_array($channels)) {
            $channelPayload = $channels[$channel] ?? null;

            if (!is_array($channelPayload)) {
                throw new RuntimeException("Release channel [{$channel}] is unavailable.");
            }

            return $channelPayload;
        }

        return $payload;
    }

    private function readSetting(string $key): mixed
    {
        return Setting::query()
            ->where('key', $key)
            ->first()
            ?->value;
    }

    private function writeSetting(string $key, mixed $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    private function normalizeStatus(mixed $status): array
    {
        $normalized = is_array($status) ? $status : [];

        return [
            'current_version' => $this->toString($normalized['current_version'] ?? config('updates.current_version', '0.0.0'), '0.0.0'),
            'latest_version' => $this->toString($normalized['latest_version'] ?? null, null),
            'checked_at' => $this->toString($normalized['checked_at'] ?? null, null),
            'last_attempted_at' => $this->toString($normalized['last_attempted_at'] ?? null, null),
            'update_available' => $this->toBool($normalized['update_available'] ?? false, false),
            'failure_message' => $this->toString($normalized['failure_message'] ?? null, null),
            'release_notes_url' => $this->toString($normalized['release_notes_url'] ?? null, null),
            'release_archive_url' => $this->toString($normalized['release_archive_url'] ?? null, null),
            'release_archive_sha256' => $this->toString($normalized['release_archive_sha256'] ?? null, null),
            'archive_verified_at' => $this->toString($normalized['archive_verified_at'] ?? null, null),
            'archive_verified_sha256' => $this->toString($normalized['archive_verified_sha256'] ?? null, null),
            'archive_verified_bytes' => is_numeric($normalized['archive_verified_bytes'] ?? null)
                ? (int) $normalized['archive_verified_bytes']
                : null,
            'archive_verified_filename' => $this->toString($normalized['archive_verified_filename'] ?? null, null),
            'archive_extraction_status' => $this->toString($normalized['archive_extraction_status'] ?? null, null),
            'archive_extraction_message' => $this->toString($normalized['archive_extraction_message'] ?? null, null),
            'archive_extracted_directory' => $this->toString($normalized['archive_extracted_directory'] ?? null, null),
            'archive_extracted_file_count' => is_numeric($normalized['archive_extracted_file_count'] ?? null)
                ? (int) $normalized['archive_extracted_file_count']
                : null,
            'manual_review_required' => $this->toBool($normalized['manual_review_required'] ?? false, false),
            'minimum_php_version' => $this->toString($normalized['minimum_php_version'] ?? null, null),
            'php_requirement_ok' => $this->toBool($normalized['php_requirement_ok'] ?? true, true),
            'manifest_source' => $this->toString($normalized['manifest_source'] ?? null, null),
            'apply_status' => $this->toString($normalized['apply_status'] ?? null, null),
            'operator_message' => $this->toString($normalized['operator_message'] ?? null, null),
            'operator_instructions' => is_array($normalized['operator_instructions'] ?? null)
                ? array_values($normalized['operator_instructions'])
                : [],
            'rollback_note' => $this->toString($normalized['rollback_note'] ?? null, null),
            'confirmed_at' => $this->toString($normalized['confirmed_at'] ?? null, null),
            'health_checked_at' => $this->toString($normalized['health_checked_at'] ?? null, null),
            'health_status' => $this->toString($normalized['health_status'] ?? null, null),
            'health_checks' => is_array($normalized['health_checks'] ?? null)
                ? $normalized['health_checks']
                : [],
            'status' => $this->toString($normalized['status'] ?? 'idle', 'idle'),
            'status_label' => $this->toString($normalized['status_label'] ?? 'Not checked yet', 'Not checked yet'),
        ];
    }

    private function resolveDriver(string $preferredDriver): UpdateDriver
    {
        if ($preferredDriver === 'coolify') {
            return new CoolifyUpdateDriver();
        }

        if ($preferredDriver === 'archive') {
            return new ArchiveUpdateDriver();
        }

        if ($preferredDriver === 'fake') {
            $fake = new FakeUpdateDriver();

            if ($fake->isConfigured()) {
                return $fake;
            }
        }

        return new ManualUpdateDriver();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function driverOptions(): array
    {
        return array_map(function (UpdateDriver $driver): array {
            $preflight = $driver->preflight([], $this->getSettings());

            return [
                'key' => $driver->key(),
                'label' => $driver->label(),
                'configured' => $driver->isConfigured(),
                'supported' => $driver->isSupported(),
                'preflight_ok' => (bool) ($preflight['ok'] ?? false),
                'supports_apply' => (bool) ($preflight['supports_apply'] ?? false),
                'message' => $this->toString($preflight['message'] ?? null, null),
            ];
        }, [
            new ManualUpdateDriver(),
            new CoolifyUpdateDriver(),
            new ArchiveUpdateDriver(),
        ]);
    }

    private function applicationBlockReason(array $status, array $settings, bool $force): ?string
    {
        if (!($status['update_available'] ?? false)) {
            return 'No update is currently available.';
        }

        if (!($status['php_requirement_ok'] ?? true)) {
            return 'The available release does not satisfy the current PHP runtime requirement.';
        }

        if ($status['manual_review_required'] ?? false) {
            return 'The available release requires manual review before application.';
        }

        if (!(bool) config('updates.auto_apply_enabled', true)) {
            return 'Update application is disabled by environment configuration.';
        }

        if (!$force && !$settings['auto_update_enabled']) {
            return 'Automatic update application is disabled in admin settings.';
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $status
     * @param  array<string, mixed>  $updates
     */
    private function writeApplyStatus(array $status, array $updates): void
    {
        $this->writeSetting(self::STATUS_KEY, [
            ...$status,
            ...$updates,
        ]);
    }

    private function toBool(mixed $value, bool $fallback): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $normalized = strtolower($value);

            if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
                return true;
            }

            if (in_array($normalized, ['0', 'false', 'no', 'off'], true)) {
                return false;
            }
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $fallback;
    }

    private function toString(mixed $value, ?string $fallback): ?string
    {
        if ($value === null) {
            return $fallback;
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return $fallback;
    }
}
