<?php

namespace App\Modules\Unit\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Register and bootstrap the Unit module services.
 *
 * This service provider is responsible for loading the module's
 * routes and database migrations during application startup.
 *
 * @package App\Modules\Unit\Providers
 * @author Sanjiv Kumar Kushwaha
 */
class UnitServiceProvider extends ServiceProvider
{
    /**
     * Register the Unit module services.
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
     * Bootstrap the Unit module services.
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
