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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'license' => [
        'url' => env('LICENSE_SYSTEM_URL'),

        'v1_application_uuid' => env('V1_LICENSE_APPLICATION_UUID'),
        'v1_identifier' => env('V1_LICENSE_API_IDENTIFIER'),
        'v1_secret' => env('V1_LICENSE_API_SECRET'),

        'v2_application_uuid' => env('V2_LICENSE_APPLICATION_UUID'),
        'v2_identifier' => env('V2_LICENSE_API_IDENTIFIER'),
        'v2_secret' => env('V2_LICENSE_API_SECRET'),
    ],

];
