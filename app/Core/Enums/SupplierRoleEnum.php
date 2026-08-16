<?php

declare(strict_types=1);

namespace App\Core\Enums;

enum SupplierRoleEnum: string
{
    case SUPPLIER_OWNER = 'supplier_owner';
    case SUPPLIER_PURCHASE_MANAGER = 'supplier_purchase_manager';
    case SUPPLIER_ACCOUNTS = 'supplier_accounts';
    case SUPPLIER_STAFF = 'supplier_staff';

    public function displayName(): string
    {
        return match ($this) {
            self::SUPPLIER_OWNER => 'Supplier Owner',
            self::SUPPLIER_PURCHASE_MANAGER => 'Supplier Purchase Manager',
            self::SUPPLIER_ACCOUNTS => 'Supplier Accounts',
            self::SUPPLIER_STAFF => 'Supplier Staff',
        };
    }
}