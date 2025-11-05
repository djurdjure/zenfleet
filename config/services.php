<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
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
    | PDF Microservice Configuration
    |--------------------------------------------------------------------------
    */
    'pdf' => [
        'url' => env('PDF_SERVICE_URL', 'http://pdf-service:3000') . '/generate-pdf',
        'health_url' => env('PDF_SERVICE_HEALTH_URL', 'http://pdf-service:3000/health'),
        'timeout' => env('PDF_SERVICE_TIMEOUT', 60),
        'retries' => env('PDF_SERVICE_RETRY', 3),
        'api_key' => env('PDF_SERVICE_API_KEY', ''),
    ],

];
