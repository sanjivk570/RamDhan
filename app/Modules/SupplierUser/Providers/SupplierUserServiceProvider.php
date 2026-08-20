<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Providers;

use Illuminate\Support\ServiceProvider;

final class SupplierUserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Services are resolved through Laravel's container.
    }

    public function boot(): void
    {
        $modulePath = dirname(__DIR__);

        if (file_exists($modulePath . '/Routes/api.php')) {
            $this->loadRoutesFrom($modulePath . '/Routes/api.php');
        }

        if (is_dir($modulePath . '/Database/Migrations')) {
            $this->loadMigrationsFrom($modulePath . '/Database/Migrations');
        }
    }
}
