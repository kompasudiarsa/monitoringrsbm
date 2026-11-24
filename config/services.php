<?php

return [
    // Jhon | 2026-11-23 20:45 WITA | TAMBAH: endpoint login SIMRS tanpa kredensial tertanam.
    'monitoring_auth' => ['url' => env('MONITORING_AUTH_URL', 'http://10.60.1.78/service/auth/login')],

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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'bpjs_antrean' => [
        'base_url' => env('BPJS_ANTREAN_BASE_URL'),
        'cons_id' => env('BPJS_CONS_ID'),
        'secret_key' => env('BPJS_SECRET_KEY'),
        'user_key' => env('BPJS_USER_KEY'),
    ],
    'bpjs_vclaim' => [
        'base_url' => env('BPJS_VCLAIM_BASE_URL'),
        'cons_id' => env('BPJS_CONS_ID'),
        'secret_key' => env('BPJS_SECRET_KEY'),
        'user_key' => env('BPJS_USER_KEY'),
    ],
    'simrs_riwayat_kontrol' => [
        'url' => env('SIMRS_RIWAYAT_KONTROL_URL'),
        'token' => env('SIMRS_RIWAYAT_KONTROL_TOKEN'),
        'cookie' => env('SIMRS_RIWAYAT_KONTROL_COOKIE'),
    ],
    'simrs_service' => [
        'url' => env('SIMRS_URL'),
        'token' => env('SIMRS_RIWAYAT_KONTROL_TOKEN'),
        'cookie' => env('SIMRS_RIWAYAT_KONTROL_COOKIE'),
    ],
    'simrs' => [
        'base_url' => env('SIMRS_URL'),
        'token' => env('SIMRS_TOKEN'),
    ],
    'bpjs_monitoring' => [
        // Di atas nilai ini dianggap lambat
        'warning_ms' => env('BPJS_MONITOR_WARNING_MS', 5000),
    ],
    'api_simrs' => [
        'base_url' => env(
            'API_BASE_URL',
            'http://127.0.0.1:8000'
        ),

        'token' => env(
            'API_TOKEN'
        ),

        'secret' => env(
            'API_SECRET'
        ),

        'timeout' => (int) env(
            'SIMRS_API_TIMEOUT',
            30
        ),
    ]
];
