<?php

return [
    // true means you are in sandbox mode. Set to false for Production
    'sandbox' => env('MPESA_SANDBOX', true),
    'c2b' => [
        // Paybill Number
        'shortcode' => env('MPESA_SHORTCODE', ''),
        // Get it at https://developer.safaricom.co.ke
        'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
        // Get it at https://developer.safaricom.co.ke
        'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
    ],
    'b2c' => [
        'shortcode' => env('MPESA_B2C_SHORTCODE', env('MPESA_SHORTCODE', '')),
        'consumer_key' => env('MPESA_B2C_CONSUMER_KEY', env('MPESA_CONSUMER_KEY', '')),
        'consumer_secret' => env('MPESA_B2C_CONSUMER_SECRET', env('MPESA_CONSUMER_SECRET', '')),
    ],
    'pull' => [
        'nominated_number' => env('MPESA_MSISDN', ''),
        'callback' => env('APP_URL') . '/payments/callbacks/pull',
    ],
    'business_shortcode' => env('MPESA_BUSINESS_SHORTCODE', ''),
    'passkey' => env('MPESA_PASSKEY', ''),
    'cache_prefix' => 'daraja',
    'validation_url' => env('APP_URL') . '/payments/callbacks/validate',
    'confirmation_url' => env('APP_URL') . '/payments/callbacks/confirmation',
    'timeout_url' => env('APP_URL') . '/payments/callbacks/timeout_url',
    'result_url' => env('APP_URL') . '/payments/callbacks/result_url',
    'stk_callback_url' => env('APP_URL') . '/payments/callbacks/stk',
    /*
     * This is the user initiating the transaction,
     * usually from the Mpesa organization portal
     * Make sure this was the user who was used to 'GO LIVE'
     * https://org.ke.m-pesa.com/
     */
    'initiator_username' => env('MPESA_INITIATOR_USERNAME', ''),
    /*
     * The user security credential.
     * Go to https://developer.safaricom.co.ke/test_credentials and
     * paste your initiator password to generate
     * security credential
     */
    'initiator_password' => env('MPESA_INITIATOR_PASS', ''),

    'notifications' => [
        'only_important' => false,
        'slack' => [
            // If set to true, run command below to install dependencies
            // composer require laravel/slack-notification-channel
            'enabled' => env('MPESA_SLACK_ENABLED', false),
            'webhook' => env('MPESA_SLACK_WEBHOOK', ''),
        ],
    ],

    // Avoids collision with existing routes
    'prefix' => 'payments/callbacks',
    'middleware' => ['web'],

];
