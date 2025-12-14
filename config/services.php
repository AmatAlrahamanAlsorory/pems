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

    // Google Services
    'google' => [
        'vision_key' => env('GOOGLE_VISION_API_KEY'),
        'video_api_key' => env('GOOGLE_VIDEO_API_KEY'),
    ],

    // OpenAI
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

    // Azure Services
    'azure' => [
        'face_api_key' => env('AZURE_FACE_API_KEY'),
        'face_endpoint' => env('AZURE_FACE_ENDPOINT'),
    ],

    // SAP Integration
    'sap' => [
        'endpoint' => env('SAP_ENDPOINT'),
        'token' => env('SAP_TOKEN'),
        'company_code' => env('SAP_COMPANY_CODE', '1000'),
    ],

    // Oracle Integration
    'oracle' => [
        'endpoint' => env('ORACLE_ENDPOINT'),
        'username' => env('ORACLE_USERNAME'),
        'password' => env('ORACLE_PASSWORD'),
    ],

    // Bank Integration
    'bank' => [
        'endpoint' => env('BANK_API_ENDPOINT'),
        'api_key' => env('BANK_API_KEY'),
    ],

    // HR System Integration
    'hr' => [
        'endpoint' => env('HR_SYSTEM_ENDPOINT'),
        'token' => env('HR_SYSTEM_TOKEN'),
    ],

    // CDN Services
    'cdn' => [
        'endpoint' => env('CDN_ENDPOINT'),
        'api_key' => env('CDN_API_KEY'),
    ],

    // Performance Monitoring
    'monitoring' => [
        'new_relic_key' => env('NEW_RELIC_LICENSE_KEY'),
        'datadog_key' => env('DATADOG_API_KEY'),
    ],

];