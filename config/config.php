<?php
/**
 * Laravel MPESA configuration file
 *
 * PHP version 7
 *
 * @category  Configuration
 * @package   LaravelMPESA
 * @author    William Mandai <wm@gitbench.com>
 * @copyright 2020 Credits https://github.com/samerior/mobile-money
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      https://github.com/wmandai/mpesa
 */

return [
    //Specify the environment mpesa is running, sandbox or production
    'sandbox' => env('MPESA_SANDBOX', true),

    /*
    |--------------------------------------------------------------------------
    | Cache credentials
    |--------------------------------------------------------------------------
    |
    | If you decide to cache credentials, they will be kept in your app cache
    | configuration for sometime. Reducing the need for many requests for
    | generating credentials
    |
     */
    'cache_credentials' => false,

    /*
    |--------------------------------------------------------------------------
    | C2B array
    |--------------------------------------------------------------------------
    |
    | If you are accepting payments enter application details and shortcode info
    |
     */
    'c2b' => [
        /*
         * Consumer Key from developer portal
         */
        'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
        /*
         * Consumer secret from developer portal
         */
        'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
        /*
         * HTTP callback method [POST,GET]
         */
        'callback_method' => 'POST',

        // MPESA Paybill Number or Lipa na MPESA number
        // If you are using Paybill enter Short Code e.g 601426
        // If using Lipa na MPESA enter Short Code e.g 174379
        'short_code' => env('MPESA_SHORTCODE', ''),

        /*
         * Passkey , requested from mpesa
         */
        'passkey' => env('MPESA_SHORTCODE_PASS', ''),
        /*
         * --------------------------------------------------------------------------------------
         * Callbacks:
         * ---------------------------------------------------------------------------------------
         * Please update your app url in .env file
         * Note: This package has already routes for handling this callback.
         * Change only if necessary
         */
        /*
         * Stk callback URL
         */
        'stk_callback' => env('APP_URL') . '/payments/callbacks/stk_callback',
        /*
         * Data is sent to this URL for successful payment
         */
        'confirmation_url' => env('APP_URL') . '/payments/callbacks/confirmation',
        /*
         * Mpesa validation URL.
         * NOTE: You need to email MPESA to enable validation
         */
        'validation_url' => env('APP_URL') . '/payments/callbacks/validate',
    ],

    /*
    |--------------------------------------------------------------------------
    | B2C array
    |--------------------------------------------------------------------------
    |
    | If you are sending payments to customers or b2b
    |
     */
    'b2c' => [
        /*
         * Sending app consumer key
         */
        'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
        /*
         * Sending app consumer secret
         */
        'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
        /*
         * Shortcode sending funds
         */
        'short_code' => env('MPESA_SHORTCODE', ''),
        /*
         * This is the user initiating the transaction,
         * usually from the Mpesa organization portal
         * Make sure this was the user who was used to 'GO LIVE'
         * https://org.ke.m-pesa.com/
         */
        'initiator' => env('MPESA_INITIATOR_USERNAME', ''),
        /*
         * The user security credential.
         * Go to https://developer.safaricom.co.ke/test_credentials and
         * paste your initiator password to generate
         * security credential
         */
        'security_credential' => env('MPESA_INITIATOR_PASS', ''),
        /*
         * Notification URL for timeout
         */
        'timeout_url' => env('APP_URL') . '/payments/callbacks/timeout/',
        /**
         * Result URL
         */
        'result_url' => env('APP_URL') . '/payments/callbacks/result/',
    ],
    /*
     * Configure slack notifications to receive mpesa events and callbacks
     */
    'notifications' => [
        /*
         * Slack webhook URL
         * https://my.slack.com/services/new/incoming-webhook/
         */
        'slack_web_hook' => env('MPESA_SLACK_URL', ''),
        /*
         * Get only important notifications
         * You wont be notified for failed stk push transactions
         */
        'only_important' => false,
    ],

];
