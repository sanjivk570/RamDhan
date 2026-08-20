<?php

namespace App\Modules\Media\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Register and bootstrap the Media module services.
 *
 * This service provider is responsible for loading the module's
 * routes and database migrations during application startup.
 *
 * @package App\Modules\Media\Providers
 * @author Sanjiv Kumar Kushwaha
 */
class MediaServiceProvider extends ServiceProvider
{
    /**
     * Register the Media module services.
     *
     * Bind any service container dependencies or module-specific
     * services here.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap the Media module services.
     *
     * Loads the module's API routes and database migrations.
     *
     * @return void
     */
    public function boot(): void
    {
        $modulePath = dirname(__DIR__);

        // Load module routes.
        if (file_exists($modulePath.'/Routes/api.php')) {
            $this->loadRoutesFrom($modulePath.'/Routes/api.php');
        }

        // Load module migrations.
        if (is_dir($modulePath.'/Database/Migrations')) {
            $this->loadMigrationsFrom($modulePath.'/Database/Migrations');
        }
    }
}
