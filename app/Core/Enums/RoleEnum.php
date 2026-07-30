<?php

declare(strict_types=1);

namespace App\Core\Enums;

/**
 * Define application user roles.
 *
 * This enum contains all user roles used for role-based
 * access control (RBAC) and provides display-friendly
 * names for each role.
 *
 * @package App\Core\Enums
 * @author Sanjiv Kumar Kushwaha
 */
enum RoleEnum: string
{
    /**
     * Application roles.
     */
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
    case VENDOR = 'vendor';
    case STAFF = 'staff';

    /**
     * Get the display name for the role.
     *
     * @return string
     */
    public function displayName(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Administrator',
            self::CUSTOMER => 'Customer',
            self::VENDOR => 'Vendor',
            self::STAFF => 'Staff',
        };
    }
}
