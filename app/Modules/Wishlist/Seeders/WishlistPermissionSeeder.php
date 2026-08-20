<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
final class WishlistPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (["wishlist.view"] as $name) {
            Permission::findOrCreate($name, "web");
        }
    }
}
