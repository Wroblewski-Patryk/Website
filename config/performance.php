<?php

return [
    'query_profiling' => [
        'enabled' => (bool) env('DB_QUERY_PROFILING_ENABLED', false),
        'slow_query_threshold_ms' => (int) env('DB_SLOW_QUERY_THRESHOLD_MS', 75),
    ],
    'response_budget' => [
        'enabled' => (bool) env('RESPONSE_BUDGET_ENABLED', false),
        'threshold_ms' => (int) env('RESPONSE_BUDGET_THRESHOLD_MS', 800),
    ],
    'perf_smoke' => [
        'threshold_ms' => (int) env('PERF_SMOKE_THRESHOLD_MS', 1200),
        'default_paths' => [
            '/en',
            '/en/blog',
            '/en/projects',
        ],
    ],
];
