<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Seeders;

use App\Core\Enums\SupplierRoleEnum;
use App\Modules\Supplier\Models\Supplier;
use App\Modules\SupplierUser\Models\SupplierUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed demo supplier users.
 *
 * Creates a primary owner plus a couple of role-based users
 * for each demo supplier so that the supplier portal can be
 * tested with the different supplier roles.
 *
 * Credentials:
 *   supplier owner  : owner.<supplier_code.lower>@ramdhan.test / Password@123
 *   purchase manager: manager.<...>>@ramdhan.test / Password@123
 *   accounts        : accounts.<...>@ramdhan.test / Password@123
 *
 * @author Sanjiv Kumar Kushwaha
 */
class SupplierUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $suppliers = Supplier::query()->where('is_active', true)->get();

        foreach ($suppliers as $supplier) {
            if (SupplierUser::where('supplier_id', $supplier->id)->exists()) {
                continue;
            }

            $base = strtolower(preg_replace('/[^A-Za-z0-9]+/', '', $supplier->supplier_code));

            // Friendly first name derived from the company name.
            $first = trim(explode(' ', preg_replace('/\s+/', ' ', $supplier->company_name))[0] ?? 'Supplier');
            $first = $first !== '' ? $first : 'Supplier';

            $users = [
                [
                    'first_name' => $first,
                    'last_name' => 'Owner',
                    'email' => "owner.{$base}@demo.ramdhan.test",
                    'role' => SupplierRoleEnum::SUPPLIER_OWNER,
                    'is_primary_supplier_user' => true,
                ],
                [
                    'first_name' => $first,
                    'last_name' => 'Purchase Manager',
                    'email' => "manager.{$base}@demo.ramdhan.test",
                    'role' => SupplierRoleEnum::SUPPLIER_PURCHASE_MANAGER,
                    'is_primary_supplier_user' => false,
                ],
                [
                    'first_name' => $first,
                    'last_name' => 'Accounts',
                    'email' => "accounts.{$base}@demo.ramdhan.test",
                    'role' => SupplierRoleEnum::SUPPLIER_ACCOUNTS,
                    'is_primary_supplier_user' => false,
                ],
            ];

            foreach ($users as $data) {
                $user = SupplierUser::updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'uuid' => (string) Str::uuid(),
                        'supplier_id' => $supplier->id,
                        'user_type' => 'supplier',
                        'is_primary_supplier_user' => $data['is_primary_supplier_user'],
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'country_code' => $supplier->country_code ?? '+91',
                        'mobile' => null,
                        // Hashed automatically by the User model cast.
                        'password' => 'Password@123',
                        'email_verified_at' => now(),
                        'is_active' => true,
                    ]
                );

                if (!$user->hasRole($data['role']->value)) {
                    $user->assignRole($data['role']->value);
                }
            }
        }
    }
}