<?php

namespace Wmandai\Mpesa;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Wmandai\Mpesa\Commands\Install;
use Wmandai\Mpesa\Commands\RegisterUrlCommand;
use Wmandai\Mpesa\Commands\StkStatusCommand;
use Wmandai\Mpesa\Events\B2cPaymentFailedEvent;
use Wmandai\Mpesa\Events\B2cPaymentSuccessEvent;
use Wmandai\Mpesa\Events\C2bConfirmationEvent;
use Wmandai\Mpesa\Events\StkPushPaymentFailedEvent;
use Wmandai\Mpesa\Events\StkPushPaymentSuccessEvent;
use Wmandai\Mpesa\Facades\MpesaFacade;
use Wmandai\Mpesa\Http\Middlewares\MobileMoneyCors;
use Wmandai\Mpesa\Library\BulkSender;
use Wmandai\Mpesa\Library\Core;
use Wmandai\Mpesa\Library\IdCheck;
use Wmandai\Mpesa\Library\RegisterUrl;
use Wmandai\Mpesa\Library\StkPush;
use Wmandai\Mpesa\Listeners\B2CFailedListener;
use Wmandai\Mpesa\Listeners\B2CSuccessListener;
use Wmandai\Mpesa\Listeners\C2bPaymentConfirmation;
use Wmandai\Mpesa\Listeners\StkPaymentFailed;
use Wmandai\Mpesa\Listeners\StkPaymentSuccessful;

class LaravelMpesaServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {

            $this->commands([
                Install::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('mpesa.php'),
            ], 'config');
            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], 'mpesa-migrations');
            // Registering package commands.
            // $this->commands([]);
            $this->app['router']->aliasMiddleware('mpesacors', MobileMoneyCors::class);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $core = new Core(new Client(['http_errors' => false]));
        $this->app->bind(Core::class, function () use ($core) {
            return $core;
        });
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    RegisterUrlCommand::class,
                    StkStatusCommand::class,
                ]
            );
        }

        $this->registerFacades();
        $this->registerEvents();

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'mpesa');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mpesa');

        // Register the main class to use with the facade
        $this->app->singleton('mpesa', function () {
            return new MpesaFacade;
        });
    }

    /**
     * Register facade accessors
     */
    protected function registerFacades()
    {
        $this->app->bind(
            'mpesa_stk',
            function () {
                return $this->app->make(StkPush::class);
            }
        );
        $this->app->bind(
            'mpesa_registrar',
            function () {
                return $this->app->make(RegisterUrl::class);
            }
        );
        $this->app->bind(
            'mpesa_identity',
            function () {
                return $this->app->make(IdCheck::class);
            }
        );
        $this->app->bind(
            'mpesa_b2c',
            function () {
                return $this->app->make(BulkSender::class);
            }
        );
    }

    /**
     * Register events
     */
    public function registerEvents()
    {
        Event::listen(StkPushPaymentSuccessEvent::class, StkPaymentSuccessful::class);
        Event::listen(StkPushPaymentFailedEvent::class, StkPaymentFailed::class);
        Event::listen(C2bConfirmationEvent::class, C2bPaymentConfirmation::class);
        Event::listen(B2cPaymentSuccessEvent::class, B2CSuccessListener::class);
        Event::listen(B2cPaymentFailedEvent::class, B2CFailedListener::class);
    }
}
