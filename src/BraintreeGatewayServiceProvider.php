<?php

namespace NimaN2D\BraintreeGateway;

use Illuminate\Support\ServiceProvider;
use NimaN2D\BraintreeGateway\Exceptions\BraintreeConfigException;
use NimaN2D\BraintreeGateway\Facades\BraintreeGateway;
use NimaN2D\BraintreeGateway\Services\CastService;
use NimaN2D\BraintreeGateway\Services\Interfaces\CastServiceInterface;

class BraintreeGatewayServiceProvider extends ServiceProvider
{
    /**
     * Register API class.
     *
     * @return void
     */
    public function register()
    {

        $this->setupConfig();
        $this->registerHelpers();
        $this->registerFacades();

    }

    /**
     * Bootstrap API resources.
     *
     * @return void
     */
    public function boot()
    {
        // Export config
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/config/braintree-gateway.php' => config_path('braintree-gateway.php'),
            ], 'braintree-gateway-config');
        }

        // Export migrations
        if ($this->shouldMigrate()) {
            $this->loadMigrationsFrom([
                __DIR__ . '/database/migrations'
            ]);
        }

        $this->app->singleton(CastServiceInterface::class, CastService::class);


        $this->publishes([
            dirname(__DIR__) . '/database/migrations/' => database_path('migrations'),
        ], 'braintree-gateway-migrations');

        $this->publishes([
            __DIR__ . '/resources/lang' => resource_path('lang/vendor/braintree-gateway'),
        ], 'braintree-gateway-lang');

        $this->loadTranslationsFrom(__DIR__ . '/resources/lang/', 'braintree-gateway');


    }

    /**
     * Set Config files.
     */
    protected function setupConfig()
    {

        $path = realpath($raw = __DIR__ . '/config/braintree-gateway.php') ?: $raw;
        $this->mergeConfigFrom($path, 'braintree-gateway');

    }


    /**
     * Register helpers.
     */
    protected function registerHelpers()
    {
        if (file_exists($helperFile = __DIR__ . '/Helpers/functions.php')) {
            require_once $helperFile;
        }
    }

    /**
     * Register facades
     */
    private function registerFacades()
    {

        $this->app->singleton('braintree_gateway', function ($app) {
            if (
                is_null(config('braintree-gateway.environment')) or
                is_null(config('braintree-gateway.merchantId')) or
                is_null(config('braintree-gateway.publicKey')) or
                is_null(config('braintree-gateway.privateKey'))
            )
                throw new BraintreeConfigException();

            return new BraintreeGateway();
        });

    }

    /**
     * Determine if we should register the migrations.
     *
     * @return bool
     */
    protected function shouldMigrate(): bool
    {
        return BraintreeConfigure::$runsMigrations;
    }

}
