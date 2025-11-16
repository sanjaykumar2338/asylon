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

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from' => env('TWILIO_FROM'),
    ],

    'telnyx' => [
        'key' => env('TELNYX_API_KEY'),
        'from' => env('TELNYX_FROM_NUMBER'),
        'messaging_profile_id' => env('TELNYX_MESSAGING_PROFILE_ID'),
        'alpha' => env('TELNYX_ALPHA_SENDER', 'ASYLON'),
        'enable_alpha' => env('TELNYX_ENABLE_ALPHA', false),
        'sms_enabled' => env('SMS_ENABLED', true),
        'skip_ssl_verify' => env('TELNYX_SKIP_SSL_VERIFY', env('APP_ENV') === 'local'),
        'timeout' => env('TELNYX_TIMEOUT', 8),
        'connect_timeout' => env('TELNYX_CONNECT_TIMEOUT', 4),
    ],

];
