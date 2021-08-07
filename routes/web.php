<?php
date_default_timezone_set('Africa/Nairobi');
use Illuminate\Support\Facades\Route;
use Wmandai\Mpesa\Http\Controllers\MpesaController;
use Wmandai\Mpesa\Http\Controllers\StkController;

Route::group(
    [
        'namespace' => 'Wmandai\Mpesa\Http\Controllers',
    ], function () {
        Route::post('validate', [MpesaController::class, 'validation'])->name('mpesa.validate');
        Route::post('confirmation', [MpesaController::class, 'confirmation'])->name('mpesa.confirmation');
        Route::any('stk_callback', [MpesaController::class, 'stkCallback'])->name('mpesa.stkCallback');
        Route::any('timeout_url/{section?', [MpesaController::class, 'timeout'])->name('mpesa.timeout');
        Route::any('result/{section?', [MpesaController::class, 'result'])->name('mpesa.result');
        Route::any('stk_request', [StkController::class, 'push'])->name('mpesa.stk.push');
        Route::get('stk_status/{id}', [StkController::class, 'status'])->name('mpesa.stk.status');
    }
);
