<?php

declare(strict_types=1);

namespace App\Modules\User\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\User\Models\User;
use App\Core\Enums\RoleEnum;
use Illuminate\Support\Str;

/**
 * Seed the default super administrator user.
 *
 * Creates or updates the primary super admin account
 * and assigns the super admin role to the user.
 *
 * @package App\Modules\User\Seeders
 * @author Sanjiv Kumar Kushwaha
 */
class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates a super administrator user and ensures
     * the required role is assigned.
     *
     * @return void
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'admin@ramdhan.local',
            ],
            [
                'uuid'       => (string) Str::uuid(),
                'first_name' => 'Super',
                'last_name'  => 'Admin',
                'mobile'     => '1111111111',
                'password'   => 'Admin@123', // hashed cast in User model
                'is_active'  => true,
            ]
        );

        if (!$user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            $user->assignRole(RoleEnum::SUPER_ADMIN->value);
        }
    }
}