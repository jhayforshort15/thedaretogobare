<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'usd'),
    ],

    'store' => [
        // Where new-order notifications are sent (defaults to the mail "from" address).
        'order_notification_email' => env('ORDER_ADMIN_EMAIL') ?: env('MAIL_FROM_ADDRESS'),
    ],

    'printify' => [
        'token' => env('PRINTIFY_API_TOKEN'),
        'shop_id' => env('PRINTIFY_SHOP_ID'),
        'webhook_secret' => env('PRINTIFY_WEBHOOK_SECRET'),
        'base_url' => env('PRINTIFY_BASE_URL', 'https://api.printify.com/v1'),
        // Send paid orders straight to print. false = they wait "On hold" for an admin to approve.
        'auto_production' => env('PRINTIFY_AUTO_PRODUCTION', true),
    ],

];
