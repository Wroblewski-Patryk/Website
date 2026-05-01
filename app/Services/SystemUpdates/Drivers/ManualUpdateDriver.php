<?php

namespace App\Services\SystemUpdates\Drivers;

use App\Services\SystemUpdates\UpdateDriver;

class ManualUpdateDriver implements UpdateDriver
{
    public function key(): string
    {
        return 'manual';
    }

    public function label(): string
    {
        return 'Manual';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function isSupported(): bool
    {
        return true;
    }

    public function preflight(array $status, array $settings): array
    {
        return [
            'ok' => true,
            'supports_apply' => false,
            'message' => 'Manual update mode is available. Automatic file replacement is not supported by this driver.',
        ];
    }

    public function apply(array $status, array $settings): array
    {
        $latestVersion = (string) ($status['latest_version'] ?? 'the latest release');
        $releaseNotesUrl = $status['release_notes_url'] ?? null;

        return [
            'ok' => true,
            'applied' => false,
            'apply_status' => 'manual_required',
            'message' => 'Manual update instructions are ready. No application files were changed.',
            'operator_instructions' => array_values(array_filter([
                "Download the trusted Featherly release for {$latestVersion}.",
                'Review the release notes and migration risk before updating.',
                'Back up the database, .env file, storage directory, and uploaded media.',
                'Deploy the release through the configured hosting workflow.',
                'Run migrations and post-deploy smoke checks.',
                is_string($releaseNotesUrl) && $releaseNotesUrl !== ''
                    ? "Release notes: {$releaseNotesUrl}"
                    : null,
            ])),
            'rollback_note' => 'Restore the previous deployment artifact and database backup if post-update smoke checks fail.',
        ];
    }
}
