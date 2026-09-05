<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Seeders;

use App\Modules\Role\Models\Permission;
use Illuminate\Database\Seeder;

final class WishlistPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['wishlist.view'] as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'display_name' => ucwords(str_replace('.', ' ', $name)),
                    'module' => 'wishlist',
                ]
            );
        }
    }
}
