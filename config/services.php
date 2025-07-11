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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'youtube' => [
        'api_key_1' => env('YOUTUBE_API_KEY_1'),
        'api_key_2' => env('YOUTUBE_API_KEY_2'),
        'api_key_3' => env('YOUTUBE_API_KEY_3'),
        'api_key_4' => env('YOUTUBE_API_KEY_4'),
        'api_key_5' => env('YOUTUBE_API_KEY_5'),
        'api_key_6' => env('YOUTUBE_API_KEY_6'),
        'api_key_7' => env('YOUTUBE_API_KEY_7'),
        ],

];
