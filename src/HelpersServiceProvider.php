<?php

namespace OnzaMe\Helpers;

use Illuminate\Support\ServiceProvider;
use OnzaMe\Helpers\Services\Contracts\RequestBetweenServicesServiceContract;
use OnzaMe\Helpers\Services\RequestBetweenServicesService;

class HelpersServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'ru');

        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('onzame_helpers.php'),
        ], 'config');

        $this->app->bind(RequestBetweenServicesServiceContract::class, RequestBetweenServicesService::class);
    }
}
