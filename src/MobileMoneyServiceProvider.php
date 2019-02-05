<?php

namespace ssemco\mobilemoney;

use Illuminate\Support\ServiceProvider;

class MobileMoneyServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'ssemco');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'ssemco');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->publishes([
            __DIR__.'/../config/mobilemoney.php' => config_path('mobilemoney.php'),
        ]);
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mobilemoney.php', 'mobilemoney');

        // Register the service the package provides.
        $this->app->singleton('mobilemoney', function ($app) {
            return new mobilemoney;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['mobilemoney'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/mobilemoney.php' => config_path('mobilemoney.php'),
        ], 'mobilemoney.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/ssemco'),
        ], 'mobilemoney.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/ssemco'),
        ], 'mobilemoney.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/ssemco'),
        ], 'mobilemoney.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
