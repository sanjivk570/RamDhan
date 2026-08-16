<?php

// declare(strict_types=1);

// namespace App\Modules\Supplier\Database;

// use Illuminate\Database\Seeder;
// use Spatie\Permission\Models\Permission;
// use Spatie\Permission\Models\Role;

// class SupplierPermissionSeeder extends Seeder
// {
//     public function run(): void
//     {
//         $permissions = [
//             'supplier.view',
//             'supplier.create',
//             'supplier.update',
//             'supplier.delete',
//             'supplier.user.view',
//             'supplier.user.create',
//             'supplier.user.update',
//             'supplier.user.delete',
//             'supplier.purchase.view',
//             'supplier.purchase.update',
//             'supplier.invoice.view',
//             'supplier.payment.view',
//         ];

//         foreach ($permissions as $permission) {
//             Permission::findOrCreate($permission, 'web');
//         }

//         $owner = Role::findOrCreate('supplier_owner', 'web');
//         $owner->syncPermissions(Permission::whereIn('name', $permissions)->get());

//         $manager = Role::findOrCreate('supplier_purchase_manager', 'web');
//         $manager->syncPermissions(Permission::whereIn('name', [
//             'supplier.view',
//             'supplier.update',
//             'supplier.user.view',
//             'supplier.purchase.view',
//             'supplier.purchase.update',
//             'supplier.invoice.view',
//         ])->get());

//         $accounts = Role::findOrCreate('supplier_accounts', 'web');
//         $accounts->syncPermissions(Permission::whereIn('name', [
//             'supplier.view',
//             'supplier.invoice.view',
//             'supplier.payment.view',
//         ])->get());

//         $staff = Role::findOrCreate('supplier_staff', 'web');
//         $staff->syncPermissions(Permission::whereIn('name', [
//             'supplier.view',
//             'supplier.purchase.view',
//         ])->get());
//     }
// }


declare(strict_types=1);

namespace App\Modules\Supplier\Seeders;

use App\Core\Enums\PermissionEnum;
use App\Modules\Role\Models\Permission;
use App\Modules\Role\Models\Role;
use App\Core\Enums\SupplierRoleEnum;


use Illuminate\Database\Seeder;

/**
 * Seed supplier role-permission assignments.
 *
 * Assigns the appropriate permissions to the
 * predefined supplier user roles.
 *
 * @package App\Modules\Supplier\Seeders
 * @author Sanjiv Kumar Kushwaha
 */
class SupplierPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Synchronizes permissions for the predefined supplier roles.
     *
     * @return void
     */
    public function run(): void
    {
        $owner = Role::findByName(
            SupplierRoleEnum::SUPPLIER_OWNER->value
        );

        $owner->syncPermissions([
            PermissionEnum::SUPPLIER_VIEW->value,
            PermissionEnum::SUPPLIER_CREATE->value,
            PermissionEnum::SUPPLIER_UPDATE->value,
            PermissionEnum::SUPPLIER_DELETE->value,

            PermissionEnum::SUPPLIER_USER_VIEW->value,
            PermissionEnum::SUPPLIER_USER_CREATE->value,
            PermissionEnum::SUPPLIER_USER_UPDATE->value,
            PermissionEnum::SUPPLIER_USER_DELETE->value,

            PermissionEnum::SUPPLIER_PURCHASE_VIEW->value,
            PermissionEnum::SUPPLIER_PURCHASE_UPDATE->value,

            PermissionEnum::SUPPLIER_INVOICE_VIEW->value,
            PermissionEnum::SUPPLIER_PAYMENT_VIEW->value,
        ]);

        $purchaseManager = Role::findByName(
            SupplierRoleEnum::SUPPLIER_PURCHASE_MANAGER->value
        );

        $purchaseManager->syncPermissions([
            PermissionEnum::SUPPLIER_VIEW->value,
            PermissionEnum::SUPPLIER_UPDATE->value,

            PermissionEnum::SUPPLIER_USER_VIEW->value,

            PermissionEnum::SUPPLIER_PURCHASE_VIEW->value,
            PermissionEnum::SUPPLIER_PURCHASE_UPDATE->value,

            PermissionEnum::SUPPLIER_INVOICE_VIEW->value,
        ]);

        $accounts = Role::findByName(
            SupplierRoleEnum::SUPPLIER_ACCOUNTS->value
        );

        $accounts->syncPermissions([
            PermissionEnum::SUPPLIER_VIEW->value,
            PermissionEnum::SUPPLIER_INVOICE_VIEW->value,
            PermissionEnum::SUPPLIER_PAYMENT_VIEW->value,
        ]);

        $staff = Role::findByName(
            SupplierRoleEnum::SUPPLIER_STAFF->value
        );

        $staff->syncPermissions([
            PermissionEnum::SUPPLIER_VIEW->value,
            PermissionEnum::SUPPLIER_PURCHASE_VIEW->value,
        ]);
    }
}