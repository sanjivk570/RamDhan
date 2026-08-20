<?php

namespace App\Modules\Cart\Providers;

use Illuminate\Support\ServiceProvider;

final class CartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $modulePath = dirname(__DIR__);
        if (file_exists($modulePath . "/Routes/api.php")) {
            $this->loadRoutesFrom($modulePath . "/Routes/api.php");
        }
        if (is_dir($modulePath . "/Database/Migrations")) {
            $this->loadMigrationsFrom($modulePath . "/Database/Migrations");
        }
    }
}
