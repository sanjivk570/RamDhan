<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

/**
 * Supplier business entity.
 *
 * Authentication is intentionally handled by User. A supplier may have
 * multiple users, while commercial information remains on this model.
 */
class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasRoles;

    //protected string $guard_name = 'web';

    protected $table = "suppliers";

    protected $primaryKey = 'id';

    protected $fillable = [
        "uuid",
        "supplier_code",
        "company_name",
        "contact_person",
        "email",
        "country_code",
        "mobile",
        "alternate_mobile",
        "website",
        "gstin",
        "pan",
        "payment_terms_days",
        "credit_limit",
        "notes",
        "is_active",
    ];

    protected function casts(): array
    {
        return [
            "payment_terms_days" => "integer",
            "credit_limit" => "decimal:2",
            "is_active" => "boolean",
            "deleted_at" => "datetime",
            "created_at" => "datetime",
            "updated_at" => "datetime",
        ];
    }

    /**
     * Generate UUID automatically.
     */
    protected static function booted(): void
    {
        static::creating(function (Supplier $supplier): void {
            if (empty($supplier->uuid)) {
                $supplier->uuid = (string) Str::uuid();
            }

            if (empty($supplier->supplier_code)) {
                $supplier->supplier_code = static::generateSupplierCode();
            }
        });
    }

    /**
     * Generate supplier code.
     */
    public static function generateSupplierCode(): string
    {
        $lastId = (int) static::withTrashed()->max("id");

        return "SUP-" . str_pad((string) ($lastId + 1), 6, "0", STR_PAD_LEFT);
    }

    /**
     * Get supplier display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->company_name;
    }

    public function primaryUser(): ?User
    {
        return $this->users()
            ->where('is_primary_supplier_user', true)
            ->first();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'supplier_id');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
