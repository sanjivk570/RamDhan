<?php

declare(strict_types=1);

namespace App\Modules\Payment\Seeders;

use App\Modules\Role\Models\Permission;
use Illuminate\Database\Seeder;

final class PaymentPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['payment.view', 'payment.refund'] as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'display_name' => ucwords(str_replace('.', ' ', $name)),
                    'module' => 'payment',
                ]
            );
        }
    }
}
