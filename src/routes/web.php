<?php
use Illuminate\Support\Facades\Route;
date_default_timezone_set('Africa/Nairobi');

Route::group([
    'prefix' => 'payments/callbacks',
    'middleware' => 'Wmandai\Mpesa\Http\Middlewares\MobileMoneyCors',
    'namespace' => 'Wmandai\Mpesa\Http\Controllers',
], function () {
    Route::any('validate', 'MpesaController@validatePayment');
    Route::any('confirmation', 'MpesaController@confirmation');
    Route::any('callback', 'MpesaController@callback');
    Route::any('stk_callback', 'MpesaController@stkCallback');
    Route::any('timeout_url/{section?}', 'MpesaController@timeout');
    Route::any('result/{section?}', 'MpesaController@result');
    Route::any('stk_request', 'StkController@initiatePush');
    Route::get('stk_status/{id}', 'StkController@stkStatus');
});
