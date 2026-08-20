<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Providers;

use App\Modules\Purchase\Contracts\InventoryStockInContract;
use App\Modules\Purchase\Services\DatabaseInventoryStockInService;
use Illuminate\Support\ServiceProvider;

final class PurchaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InventoryStockInContract::class, DatabaseInventoryStockInService::class);
    }

    public function boot(): void
    {
        $modulePath = dirname(__DIR__);
        if (file_exists($modulePath.'/Routes/api.php')) $this->loadRoutesFrom($modulePath.'/Routes/api.php');
        if (is_dir($modulePath.'/Database/Migrations')) $this->loadMigrationsFrom($modulePath.'/Database/Migrations');
    }
}
