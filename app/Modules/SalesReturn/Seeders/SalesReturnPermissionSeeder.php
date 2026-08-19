<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
final class SalesReturnPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (["return.view", "return.process"] as $name) {
            Permission::findOrCreate($name, "web");
        }
    }
}
