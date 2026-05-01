<?php

namespace App\Services\SystemUpdates;

interface UpdateDriver
{
    public function key(): string;

    public function label(): string;

    public function isConfigured(): bool;

    public function isSupported(): bool;

    /**
     * @param  array<string, mixed>  $status
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public function preflight(array $status, array $settings): array;

    /**
     * @param  array<string, mixed>  $status
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public function apply(array $status, array $settings): array;
}
