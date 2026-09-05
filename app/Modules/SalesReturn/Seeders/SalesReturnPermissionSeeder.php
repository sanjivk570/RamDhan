<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Seeders;

use App\Modules\Role\Models\Permission;
use Illuminate\Database\Seeder;

final class SalesReturnPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['return.view', 'return.process'] as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'display_name' => ucwords(str_replace('.', ' ', $name)),
                    'module' => 'return',
                ]
            );
        }
    }
}
