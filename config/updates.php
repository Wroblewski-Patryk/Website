<?php

return [
    'manifest_url' => env('FEATHERLY_UPDATE_MANIFEST_URL'),
    'default_channel' => env('FEATHERLY_UPDATE_CHANNEL', 'stable'),
    'auto_apply_enabled' => (bool) env('FEATHERLY_AUTO_UPDATES_ENABLED', true),
    'current_version' => env('APP_VERSION', '0.0.0'),
    'enable_fake_driver' => env('FEATHERLY_ENABLE_FAKE_UPDATE_DRIVER', false),
    'drivers' => [
        'coolify' => [
            'deploy_webhook_url' => env('FEATHERLY_UPDATE_COOLIFY_WEBHOOK_URL'),
            'apply_enabled' => env('FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED', false),
        ],
        'archive' => [
            'staging_path' => env('FEATHERLY_UPDATE_ARCHIVE_STAGING_PATH'),
            'release_path' => env('FEATHERLY_UPDATE_ARCHIVE_RELEASE_PATH'),
        ],
    ],
];
