<?php

declare(strict_types=1);

namespace App\Modules\Payment\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
final class PaymentPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (["payment.view", "payment.refund"] as $name) {
            Permission::findOrCreate($name, "web");
        }
    }
}
