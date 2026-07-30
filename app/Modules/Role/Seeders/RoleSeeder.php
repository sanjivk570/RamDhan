<?php

declare(strict_types=1);

namespace App\Modules\Role\Seeders;

use Illuminate\Database\Seeder;
use App\Core\Enums\RoleEnum;
use App\Modules\Role\Models\Role;

/**
 * Seed the application's roles.
 *
 * Creates all predefined roles defined in the
 * RoleEnum if they do not already exist.
 *
 * @package App\Modules\Role\Seeders
 * @author Sanjiv Kumar Kushwaha
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates role records using the values
     * defined in the RoleEnum.
     *
     * @return void
     */
    public function run(): void
    {
        
        foreach (RoleEnum::cases() as $role) {

            Role::firstOrCreate(
                [
                    'name' => $role->value,
                    'guard_name' => 'web',
                ],
                [
                    'display_name' => $role->displayName(),
                    'is_system' => true,
                ]
            );
        }
    }
}