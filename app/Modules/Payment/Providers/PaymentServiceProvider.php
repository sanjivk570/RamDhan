<?php

namespace App\Modules\Payment\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Payment\Contracts\PaymentGatewayContract;
use App\Modules\Payment\Services\DatabasePaymentGateway;


final class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PaymentGatewayContract::class,
            DatabasePaymentGateway::class
        );
        
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
