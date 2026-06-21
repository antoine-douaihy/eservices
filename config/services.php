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

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'brevo' => [
        'key' => env('BREVO_API_KEY'),
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

    'stripe' => [
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'crypto' => [
        'btc_wallet' => env('CRYPTO_BTC_WALLET', ''),
        'eth_wallet' => env('CRYPTO_ETH_WALLET', ''),
    ],

    'local_payments' => [
        'wish_account' => env('WISH_ACCOUNT', 'WISH-ACCOUNT-PLACEHOLDER'),
        'omt_account'  => env('OMT_ACCOUNT', 'OMT-ACCOUNT-PLACEHOLDER'),
    ],

    'twilio' => [
        'sid'           => env('TWILIO_SID'),
        'auth_token'    => env('TWILIO_AUTH_TOKEN'),
        // Twilio Sandbox default — replace with your own approved
        // WhatsApp sender once you move off the sandbox.
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM', '14155238886'),
        // Approved Content Template SID (HX...). Required for
        // business-initiated messages (e.g. "your service is ready")
        // outside the Sandbox. Leave blank to send plain Body text
        // (Sandbox / 24h-session mode only).
        'content_sid'   => env('TWILIO_CONTENT_SID'),
    ],

];
