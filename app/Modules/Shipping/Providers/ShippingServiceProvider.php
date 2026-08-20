<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Providers;

use Illuminate\Support\ServiceProvider;

class ShippingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
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
