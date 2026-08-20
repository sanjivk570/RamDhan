<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Seeders;

use App\Core\Enums\PermissionEnum;
use App\Core\Enums\SupplierRoleEnum;
use App\Modules\Role\Models\Permission;
use App\Modules\Role\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Seed purchase role-permission assignments.
 *
 * Assigns the appropriate permissions to the
 * predefined purchase user roles.
 *
 * @package App\Modules\Purchase\Seeders
 */
class PurchasePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Synchronizes permissions for the predefined purchase roles.
     *
     * @return void
     */
    public function run(): void
    {
        $owner = Role::findByName(
            SupplierRoleEnum::SUPPLIER_OWNER->value
        );

        $owner->syncPermissions([
            PermissionEnum::PURCHASE_VIEW->value,
            PermissionEnum::PURCHASE_CREATE->value,
            PermissionEnum::PURCHASE_UPDATE->value,
            PermissionEnum::PURCHASE_SUBMIT->value,
            PermissionEnum::PURCHASE_APPROVE->value,
            PermissionEnum::PURCHASE_CANCEL->value,

            PermissionEnum::PURCHASE_GRN_VIEW->value,
            PermissionEnum::PURCHASE_GRN_CREATE->value,
            PermissionEnum::PURCHASE_GRN_POST->value,
            PermissionEnum::PURCHASE_GRN_VOID->value,

            PermissionEnum::PURCHASE_INVOICE_VIEW->value,
            PermissionEnum::PURCHASE_INVOICE_CREATE->value,
            PermissionEnum::PURCHASE_INVOICE_POST->value,

            PermissionEnum::PURCHASE_PAYMENT_VIEW->value,
            PermissionEnum::PURCHASE_PAYMENT_CREATE->value,

            PermissionEnum::PURCHASE_RETURN_VIEW->value,
            PermissionEnum::PURCHASE_RETURN_CREATE->value,
            PermissionEnum::PURCHASE_RETURN_POST->value,
        ]);

        $purchaseManager = Role::findByName(
            SupplierRoleEnum::SUPPLIER_PURCHASE_MANAGER->value
        );

        $purchaseManager->syncPermissions([
            PermissionEnum::PURCHASE_VIEW->value,
            PermissionEnum::PURCHASE_CREATE->value,
            PermissionEnum::PURCHASE_UPDATE->value,
            PermissionEnum::PURCHASE_SUBMIT->value,
            PermissionEnum::PURCHASE_APPROVE->value,
            PermissionEnum::PURCHASE_CANCEL->value,

            PermissionEnum::PURCHASE_GRN_VIEW->value,
            PermissionEnum::PURCHASE_GRN_CREATE->value,
            PermissionEnum::PURCHASE_GRN_POST->value,
            PermissionEnum::PURCHASE_GRN_VOID->value,

            PermissionEnum::PURCHASE_INVOICE_VIEW->value,
            PermissionEnum::PURCHASE_INVOICE_CREATE->value,
            PermissionEnum::PURCHASE_INVOICE_POST->value,

            PermissionEnum::PURCHASE_PAYMENT_VIEW->value,
            PermissionEnum::PURCHASE_PAYMENT_CREATE->value,

            PermissionEnum::PURCHASE_RETURN_VIEW->value,
            PermissionEnum::PURCHASE_RETURN_CREATE->value,
            PermissionEnum::PURCHASE_RETURN_POST->value,
        ]);

        $accounts = Role::findByName(
            SupplierRoleEnum::SUPPLIER_ACCOUNTS->value
        );

        $accounts->syncPermissions([
            PermissionEnum::PURCHASE_VIEW->value,

            PermissionEnum::PURCHASE_GRN_VIEW->value,

            PermissionEnum::PURCHASE_INVOICE_VIEW->value,
            PermissionEnum::PURCHASE_INVOICE_CREATE->value,
            PermissionEnum::PURCHASE_INVOICE_POST->value,

            PermissionEnum::PURCHASE_PAYMENT_VIEW->value,
            PermissionEnum::PURCHASE_PAYMENT_CREATE->value,

            PermissionEnum::PURCHASE_RETURN_VIEW->value,
        ]);

        $staff = Role::findByName(
            SupplierRoleEnum::SUPPLIER_STAFF->value
        );

        $staff->syncPermissions([
            PermissionEnum::PURCHASE_VIEW->value,

            PermissionEnum::PURCHASE_GRN_VIEW->value,
            PermissionEnum::PURCHASE_GRN_CREATE->value,
        ]);
    }
}