<?php

declare(strict_types=1);

namespace App\Modules\Cart\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
final class CartPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (["cart.view", "cart.manage"] as $name) {
            Permission::findOrCreate($name, "web");
        }
    }
}
