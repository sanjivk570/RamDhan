<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
final class PromotionPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (
            ["coupon.view", "coupon.create", "coupon.update", "coupon.delete"]
            as $name
        ) {
            Permission::findOrCreate($name, "web");
        }
    }
}
