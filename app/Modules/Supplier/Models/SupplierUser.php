<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Models;

use App\Modules\User\Models\User;
use Spatie\Permission\Traits\HasRoles;

/**
 * Supplier user is an application-level alias for the existing User model.
 * Keep authentication and Spatie permissions on User; this class is useful
 * only when a supplier-specific type hint is helpful.
 */
class SupplierUser extends User
{
    use HasRoles;

    protected string $guard_name = 'web';

    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('supplier_user', function ($query): void {
            $query->where('user_type', 'supplier');
        });
    }
}
