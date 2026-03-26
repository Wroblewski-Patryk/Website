<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Installer Lock File
    |--------------------------------------------------------------------------
    |
    | Presence of this file marks application as already installed.
    |
    */
    'lock_file' => storage_path('app/installer.lock'),

    /*
    |--------------------------------------------------------------------------
    | Environment File
    |--------------------------------------------------------------------------
    |
    | Target .env file updated by the web installer finalize step.
    |
    */
    'env_file' => base_path('.env'),

    /*
    |--------------------------------------------------------------------------
    | Testing Bypass
    |--------------------------------------------------------------------------
    |
    | Keep default test suite stable unless a specific test overrides this.
    |
    */
    'bypass_in_testing' => true,
];
