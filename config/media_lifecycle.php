<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Media Lifecycle Policy
    |--------------------------------------------------------------------------
    |
    | These values define metadata lifecycle windows for media rows.
    | The lifecycle command runs in dry-run mode by default unless --apply
    | is explicitly passed.
    |
    */
    'archive_after_days' => (int) env('MEDIA_ARCHIVE_AFTER_DAYS', 180),
    'retention_days' => (int) env('MEDIA_RETENTION_DAYS', 180),
    'purge_after_retention_days' => (int) env('MEDIA_PURGE_AFTER_RETENTION_DAYS', 30),
];
