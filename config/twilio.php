<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twilio Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Twilio SMS service integration
    |
    */

    'enabled' => env('TWILIO_ENABLED', false),
    'sid' => env('TWILIO_SID'),
    'token' => env('TWILIO_TOKEN'),
    'from' => env('TWILIO_FROM'),
    
    /*
    |--------------------------------------------------------------------------
    | SMS Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for SMS notifications
    |
    */
    
    'max_length' => 160,
    'retry_attempts' => 3,
    'retry_delay' => 60, // seconds
];
