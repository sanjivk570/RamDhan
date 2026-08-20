<?php

namespace App\Modules\Role\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Register and bootstrap the Role module services.
 *
 * This service provider is responsible for loading the
 * Role module's routes and database migrations during
 * application startup.
 *
 * @package App\Modules\Role\Providers
 * @author Sanjiv Kumar Kushwaha
 */
class RoleServiceProvider extends ServiceProvider
{

    /**
     * Register the Role module services.
     *
     * Bind any module-specific services or dependencies
     * into the service container.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap the Role module services.
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
