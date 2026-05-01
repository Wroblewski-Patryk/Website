<?php

namespace App\Services\SystemUpdates\Drivers;

use App\Services\SystemUpdates\UpdateDriver;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CoolifyUpdateDriver implements UpdateDriver
{
    public function key(): string
    {
        return 'coolify';
    }

    public function label(): string
    {
        return 'Coolify';
    }

    public function isConfigured(): bool
    {
        return $this->deployWebhookUrl() !== '';
    }

    public function isSupported(): bool
    {
        return $this->isConfigured();
    }

    public function preflight(array $status, array $settings): array
    {
        if (!$this->isConfigured()) {
            return [
                'ok' => false,
                'supports_apply' => false,
                'message' => 'Coolify driver requires FEATHERLY_UPDATE_COOLIFY_WEBHOOK_URL.',
            ];
        }

        return [
            'ok' => true,
            'supports_apply' => $this->applyEnabled(),
            'message' => $this->applyEnabled()
                ? 'Coolify deploy webhook is configured and apply trigger is enabled.'
                : 'Coolify deploy webhook is configured. Set FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED=true to allow server-side apply.',
        ];
    }

    public function apply(array $status, array $settings): array
    {
        if (!$this->applyEnabled()) {
            return [
                'ok' => false,
                'applied' => false,
                'apply_status' => 'preflight_only',
                'message' => 'Coolify apply trigger is disabled by environment configuration.',
                'operator_instructions' => [
                    'Set FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED=true only after webhook, rollback, and smoke checks are configured.',
                    'Use Coolify manually until the trigger is enabled.',
                ],
                'rollback_note' => 'Use Coolify deployment history to roll back after a failed manual deployment.',
            ];
        }

        $response = Http::timeout(10)->post($this->deployWebhookUrl(), [
            'source' => 'featherly',
            'target_version' => $status['latest_version'] ?? null,
            'current_version' => $status['current_version'] ?? null,
            'channel' => $settings['update_release_channel'] ?? null,
        ]);

        if (!$response->successful()) {
            throw new RuntimeException('Coolify deploy webhook request failed.');
        }

        return [
            'ok' => true,
            'applied' => false,
            'apply_status' => 'deployment_triggered',
            'message' => 'Coolify deployment was triggered. Version will update after the deployment completes and health checks pass.',
            'operator_instructions' => [
                'Monitor the Coolify deployment until it finishes.',
                'Run post-deploy smoke checks after the application restarts.',
                'Confirm the deployed APP_VERSION matches the target release.',
            ],
            'rollback_note' => 'Use Coolify deployment history to roll back after a failed manual deployment.',
        ];
    }

    private function deployWebhookUrl(): string
    {
        $value = config('updates.drivers.coolify.deploy_webhook_url');

        return is_string($value) ? trim($value) : '';
    }

    private function applyEnabled(): bool
    {
        return (bool) config('updates.drivers.coolify.apply_enabled', false);
    }
}
