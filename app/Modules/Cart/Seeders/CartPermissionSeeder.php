<?php

declare(strict_types=1);

namespace App\Modules\Cart\Seeders;

use App\Modules\Role\Models\Permission;
use Illuminate\Database\Seeder;

final class CartPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['cart.view', 'cart.manage'] as $name) {
            $permission = Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'display_name' => ucwords(str_replace('.', ' ', $name)),
                    'module' => 'cart',
                ]
            );
        }
    }
}
