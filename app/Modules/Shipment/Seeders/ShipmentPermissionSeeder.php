<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
final class ShipmentPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (
            ["shipment.view", "shipment.create", "shipment.update"]
            as $name
        ) {
            Permission::findOrCreate($name, "web");
        }
    }
}
