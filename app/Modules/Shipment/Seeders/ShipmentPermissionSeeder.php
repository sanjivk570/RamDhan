<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Seeders;

use App\Modules\Role\Models\Permission;
use Illuminate\Database\Seeder;

final class ShipmentPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['shipment.view', 'shipment.create', 'shipment.update'] as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'display_name' => ucwords(str_replace('.', ' ', $name)),
                    'module' => 'shipment',
                ]
            );
        }
    }
}
