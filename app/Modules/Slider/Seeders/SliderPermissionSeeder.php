<?php

declare(strict_types=1);

namespace App\Modules\Slider\Seeders;

use App\Core\Enums\PermissionEnum;
use App\Core\Enums\RoleEnum;
use App\Modules\Role\Models\Permission;
use App\Modules\Role\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Seed slider role-permission assignments.
 *
 * Assigns the slider management permissions to the predefined
 * application roles. The base permission records are ensured here
 * (created on demand from the PermissionEnum) so this seeder can
 * also be executed independently of the Role module's PermissionSeeder.
 *
 * @package App\Modules\Slider\Seeders
 * @author Sanjiv Kumar Kushwaha
 */
class SliderPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $sliderPermissions = [
            PermissionEnum::SLIDER_VIEW->value,
            PermissionEnum::SLIDER_CREATE->value,
            PermissionEnum::SLIDER_UPDATE->value,
            PermissionEnum::SLIDER_DELETE->value,
            PermissionEnum::SLIDER_RESTORE->value,
        ];

        // Ensure the base permission records exist so this seeder can
        // be executed independently of the Role module's PermissionSeeder.
        foreach ($sliderPermissions as $sliderPermission) {
            $permission = Permission::findOrCreate($sliderPermission, 'web');

            if (!$permission->display_name) {
                $permission->forceFill([
                    'display_name' => ucwords(
                        str_replace('.', ' ', $sliderPermission)
                    ),
                    'module' => 'slider',
                ])->save();
            }
        }

        // Super admin always has access to everything.
        $superAdmin = Role::findByName(RoleEnum::SUPER_ADMIN->value);
        $superAdmin->givePermissionTo($sliderPermissions);

        // Administrators can fully manage sliders.
        $admin = Role::findByName(RoleEnum::ADMIN->value);
        $admin->givePermissionTo($sliderPermissions);
    }
}
