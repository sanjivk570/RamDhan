<?php

declare(strict_types=1);

namespace App\Modules\Role\Seeders;

use Illuminate\Database\Seeder;
use App\Core\Enums\RoleEnum;
use App\Modules\Role\Models\Role;
use App\Modules\Role\Models\Permission;
use App\Core\Enums\PermissionEnum;

/**
 * Seed role-permission assignments.
 *
 * Assigns the appropriate permissions to the
 * predefined application roles.
 *
 * @package App\Modules\Role\Seeders
 * @author Sanjiv Kumar Kushwaha
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Synchronizes permissions for the predefined roles.
     *
     * @return void
     */
    public function run(): void
    {
        $superAdmin = Role::findByName(
            RoleEnum::SUPER_ADMIN->value
        );

        $superAdmin->syncPermissions(
            Permission::all()
        );
        
        $admin = Role::findByName(RoleEnum::ADMIN->value);
        $admin->syncPermissions([
            PermissionEnum::USER_VIEW->value,
            PermissionEnum::USER_CREATE->value,
            PermissionEnum::USER_UPDATE->value,
            PermissionEnum::USER_DELETE->value,
        ]);
    }
}