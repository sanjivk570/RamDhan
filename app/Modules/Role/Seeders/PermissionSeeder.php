<?php

declare(strict_types=1);

namespace App\Modules\Role\Seeders;

use Illuminate\Database\Seeder;
use App\Core\Enums\PermissionEnum;
use App\Modules\Role\Models\Permission;

/**
 * Seed the application's permissions.
 *
 * Creates all permissions defined in the PermissionEnum
 * if they do not already exist.
 *
 * @package App\Modules\Role\Seeders
 * @author Sanjiv Kumar Kushwaha
 */
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates permission records using the values
     * defined in the PermissionEnum.
     *
     * @return void
     */
    public function run(): void
    {
        foreach (PermissionEnum::cases() as $permission) {

            Permission::firstOrCreate(
                [
                    'name' => $permission->value,
                    'guard_name' => 'web',
                ],
                [
                    'display_name' => ucwords(str_replace('.', ' ', $permission->value)),
                    'module' => explode('.', $permission->value)[0],
                ]
            );
        }
    }
}
