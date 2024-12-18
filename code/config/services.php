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

    'test_url' => env('TEST_URL', 'https://default.example.com'),
    'restaurant' => [
        'oishii' => env('RESTAURANT_OISHII_DOMAIN'),
        'steakhome' => env('RESTAURANT_STEAKHOME_DOMAIN'),
        'tasty' => env('RESTAURANT_TASTY_DOMAIN'),
    ],

    'hash_key' => env('HASH_KEY'),
    'hash_iv' => env('HASH_IV'),

    'recharge_url' => env('RECHARGE_URL'),
];
