<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;
use Throwable;

class InstallationState
{
    public function isInstalled(): bool
    {
        if (app()->runningUnitTests() && (bool) config('installer.bypass_in_testing', true)) {
            return true;
        }

        $lockFile = (string) config('installer.lock_file', storage_path('app/installer.lock'));
        if ($lockFile !== '' && is_file($lockFile)) {
            return true;
        }

        // Fallback for existing deployments that do not yet have installer lock file.
        try {
            if (!Schema::hasTable('users')) {
                return false;
            }

            return \App\Models\User::query()->exists();
        } catch (Throwable) {
            return false;
        }
    }
}
