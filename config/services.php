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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],
    'bot_service' => [
        'url' => env('BOT_SERVICE_BASE_URL', env('BOT_SERVICE_URL', env('WHATSAPP_SERVICE_URL', 'http://localhost:3000'))),
        'key' => env('BOT_SERVICE_API_KEY'),
    ],
    'whatsapp' => [
        'url' => env('WHATSAPP_SERVICE_URL', env('BOT_SERVICE_URL', 'http://localhost:3000')),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'discord' => [],

    'telegram' => [],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
