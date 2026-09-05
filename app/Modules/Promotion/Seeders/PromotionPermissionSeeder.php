<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Seeders;

use App\Modules\Role\Models\Permission;
use Illuminate\Database\Seeder;

final class PromotionPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['coupon.view', 'coupon.create', 'coupon.update', 'coupon.delete'] as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'display_name' => ucwords(str_replace('.', ' ', $name)),
                    'module' => 'promotion',
                ]
            );
        }
    }
}
