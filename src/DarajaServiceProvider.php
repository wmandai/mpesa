<?php

namespace Wmandai\Mpesa;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Wmandai\Mpesa\Console\DarajaRegisterUrl;
use Wmandai\Mpesa\Console\InstallMpesaPackage;
use Wmandai\Mpesa\Console\StkStatusCommand;
use Wmandai\Mpesa\Http\Middleware\MpesaCors;
use Wmandai\Mpesa\Providers\EventServiceProvider;

class DarajaServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     * @todo make migrations exportable
     */
    public function boot()
    {
        $this->registerRoutes();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mpesa');
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    InstallMpesaPackage::class,
                    DarajaRegisterUrl::class,
                    StkStatusCommand::class,
                ]
            );

            $this->publishes(
                [
                    __DIR__ . '/../config/config.php' => config_path('mpesa.php'),
                ],
                'config'
            );
            // Publish views
            $this->publishes(
                [
                    __DIR__ . '/../resources/views' => resource_path('views/vendor/mpesa'),
                ],
                'views'
            );
            // $this->app['router']->aliasMiddleware('mpesacors', MpesaCors::class);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'mpesa');

        // Register the main class to use with the facade
        $this->app->bind(
            'mpesa',
            function () {
                return new Daraja();
            }
        );
    }

    protected function registerRoutes()
    {
        Route::group(
            $this->routeConfiguration(),
            function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            }
        );
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('mpesa.prefix'),
            'middleware' => config('mpesa.middleware'),
        ];
    }
}
