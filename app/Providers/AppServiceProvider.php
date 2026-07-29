<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Register and bootstrap application services.
 *
 * This service provider is responsible for registering
 * application-wide services and performing global bootstrapping
 * during the application's startup.
 *
 * @package App\Providers
 * @author Sanjiv Kumar Kushwaha
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * Bind application-wide services into the service container.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap application services.
     *
     * Perform any application-wide initialization after all
     * services have been registered.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
