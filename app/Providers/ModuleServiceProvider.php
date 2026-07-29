<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

/**
 * Register and bootstrap application modules.
 *
 * This service provider automatically discovers and registers
 * service providers for all modules located in the
 * application's Modules directory.
 *
 * @package App\Providers
 * @author Sanjiv Kumar Kushwaha
 */
class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register all discovered module service providers.
     *
     * Scans the application's Modules directory and registers
     * each module's service provider if it exists.
     *
     * @return void
     */
    public function register(): void
    {
        $modulesPath = app_path('Modules');
        if (!File::exists($modulesPath)) {
            return;
        }
        foreach (File::directories($modulesPath) as $module) {
            $moduleName = basename($module);
            $provider = "App\\Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider";
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    /**
     * Bootstrap application modules.
     *
     * Perform any module-specific bootstrapping after all
     * service providers have been registered.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
