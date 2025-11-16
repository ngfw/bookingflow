<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sentry Error Tracking
    |--------------------------------------------------------------------------
    |
    | Sentry provides real-time error tracking and monitoring for production
    | applications. Configure your DSN and performance monitoring settings.
    |
    */

    'sentry' => [
        'dsn' => env('SENTRY_LARAVEL_DSN'),

        // Percentage of transactions to trace (0.0 to 1.0)
        // 0.2 = 20% of transactions will be traced
        'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE', 0.2),

        // Send default PII (Personally Identifiable Information)
        // Set to false in production for GDPR compliance
        'send_default_pii' => env('SENTRY_SEND_DEFAULT_PII', false),

        // Environment name for Sentry
        'environment' => env('APP_ENV', 'production'),

        // Release version tracking
        'release' => env('SENTRY_RELEASE', config('app.version', '1.0.0')),

        // Sample rate for profiling (0.0 to 1.0)
        'profiles_sample_rate' => env('SENTRY_PROFILES_SAMPLE_RATE', 0.1),
    ],

    /*
    |--------------------------------------------------------------------------
    | New Relic APM (Application Performance Monitoring)
    |--------------------------------------------------------------------------
    |
    | New Relic provides comprehensive application performance monitoring.
    | Configure your license key and application name.
    |
    */

    'newrelic' => [
        'enabled' => env('NEW_RELIC_ENABLED', false),
        'license_key' => env('NEW_RELIC_LICENSE_KEY'),
        'app_name' => env('NEW_RELIC_APP_NAME', config('app.name')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Twilio SMS Service
    |--------------------------------------------------------------------------
    |
    | Twilio is used for sending SMS notifications, appointment reminders,
    | and two-factor authentication codes.
    |
    */

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from' => env('TWILIO_FROM'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Stripe configuration for processing payments, subscriptions,
    | and handling webhooks.
    |
    */

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

];
