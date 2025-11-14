<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for various monitoring and observability
    | tools used in production environments.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Error Tracking (Sentry)
    |--------------------------------------------------------------------------
    */
    'sentry' => [
        'enabled' => env('SENTRY_LARAVEL_DSN') !== null,
        'dsn' => env('SENTRY_LARAVEL_DSN'),
        'environment' => env('APP_ENV', 'production'),
        'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.2),
        'send_default_pii' => false,
        'breadcrumbs' => [
            'sql_queries' => true,
            'sql_bindings' => false,
            'logs' => true,
            'cache' => false,
        ],
        'release' => env('APP_VERSION', '1.0.0'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring (New Relic)
    |--------------------------------------------------------------------------
    */
    'newrelic' => [
        'enabled' => env('NEW_RELIC_LICENSE_KEY') !== null,
        'license_key' => env('NEW_RELIC_LICENSE_KEY'),
        'app_name' => env('NEW_RELIC_APP_NAME', env('APP_NAME')),
        'capture_params' => env('NEW_RELIC_CAPTURE_PARAMS', false),
        'ignored_params' => ['password', 'password_confirmation', 'token'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Uptime Monitoring
    |--------------------------------------------------------------------------
    */
    'uptime' => [
        'enabled' => true,
        'check_urls' => [
            env('APP_URL'),
            env('APP_URL') . '/api/health',
            env('APP_URL') . '/book',
        ],
        'notification_email' => env('UPTIME_NOTIFICATION_EMAIL', 'admin@example.com'),
        'notification_slack' => env('UPTIME_NOTIFICATION_SLACK'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Performance Monitoring
    |--------------------------------------------------------------------------
    */
    'apm' => [
        'enabled' => env('APP_ENV') === 'production',
        'slow_query_threshold' => 1000, // milliseconds
        'slow_route_threshold' => 2000, // milliseconds
        'memory_limit_warning' => 128, // MB
        'log_slow_queries' => true,
        'log_n_plus_one' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Checks
    |--------------------------------------------------------------------------
    */
    'health' => [
        'enabled' => true,
        'checks' => [
            'database' => true,
            'redis' => true,
            'storage' => true,
            'queue' => true,
            'mail' => false, // Don't check mail in health endpoint
        ],
        'cache_ttl' => 60, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics Collection
    |--------------------------------------------------------------------------
    */
    'metrics' => [
        'enabled' => env('APP_ENV') === 'production',
        'track' => [
            'appointments' => true,
            'bookings' => true,
            'payments' => true,
            'users' => true,
            'services' => true,
            'inventory' => true,
        ],
        'aggregation_interval' => 300, // seconds (5 minutes)
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Aggregation
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'papertrail' => [
            'enabled' => env('PAPERTRAIL_URL') !== null,
            'url' => env('PAPERTRAIL_URL'),
            'port' => env('PAPERTRAIL_PORT', 514),
        ],
        'logtail' => [
            'enabled' => env('LOGTAIL_SOURCE_TOKEN') !== null,
            'source_token' => env('LOGTAIL_SOURCE_TOKEN'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Real User Monitoring (RUM)
    |--------------------------------------------------------------------------
    */
    'rum' => [
        'enabled' => env('APP_ENV') === 'production',
        'sample_rate' => 0.1, // 10% of users
        'track_user_interactions' => true,
        'track_ajax_requests' => true,
        'track_errors' => true,
    ],

];
