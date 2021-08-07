<?php

namespace Wmandai\Mpesa\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Wmandai\Mpesa\Events\B2cPaymentFailedEvent;
use Wmandai\Mpesa\Events\B2cPaymentSuccessEvent;
use Wmandai\Mpesa\Events\C2bValidationEvent;
use Wmandai\Mpesa\Events\MpesaConfirmationEvent;
use Wmandai\Mpesa\Events\MpesaValidationEvent;
use Wmandai\Mpesa\Events\StkPushPaymentFailedEvent;
use Wmandai\Mpesa\Events\StkPushPaymentSuccessEvent;
use Wmandai\Mpesa\Listeners\B2CFailedListener;
use Wmandai\Mpesa\Listeners\B2CSuccessListener;
use Wmandai\Mpesa\Listeners\MpesaConfirmationListener;
use Wmandai\Mpesa\Listeners\MpesaValidationListener;
use Wmandai\Mpesa\Listeners\StkPaymentFailed;
use Wmandai\Mpesa\Listeners\StkPaymentSuccessful;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        StkPushPaymentSuccessEvent::class => [
            StkPaymentSuccessful::class,
        ],
        StkPushPaymentFailedEvent::class => [
            StkPaymentFailed::class,
        ],
        MpesaConfirmationEvent::class => [
            MpesaConfirmationListener::class,
        ],
        MpesaValidationEvent::class => [
            MpesaValidationListener::class,
        ],
        B2cPaymentSuccessEvent::class => [
            B2CSuccessListener::class,
        ],
        B2cPaymentFailedEvent::class => [
            B2CFailedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
