<?php

namespace App\Services\SystemUpdates\Drivers;

use App\Services\SystemUpdates\UpdateDriver;

class FakeUpdateDriver implements UpdateDriver
{
    public function key(): string
    {
        return 'fake';
    }

    public function label(): string
    {
        return 'Fake';
    }

    public function isConfigured(): bool
    {
        return (bool) config('updates.enable_fake_driver', false);
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
                'message' => 'Fake update driver is disabled.',
            ];
        }

        return [
            'ok' => true,
            'supports_apply' => true,
            'message' => 'Fake update driver preflight passed.',
        ];
    }

    public function apply(array $status, array $settings): array
    {
        return [
            'ok' => true,
            'applied' => true,
            'apply_status' => 'applied',
            'message' => 'Fake update application completed.',
            'operator_instructions' => [],
            'rollback_note' => 'No runtime files were changed by the fake update driver.',
        ];
    }
}
