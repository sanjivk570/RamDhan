<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Models;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Supplier user model backed by the existing users table.
 *
 * This model never creates a second authentication table. It scopes
 * application queries to supplier users while preserving Sanctum and
 * Spatie Permission on the existing User model.
 */
class SupplierUser extends User
{
    protected $table = 'users';
    
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('supplier_user', function (Builder $query): void {
            $query->where($query->getModel()->getTable() . '.user_type', 'supplier');
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
