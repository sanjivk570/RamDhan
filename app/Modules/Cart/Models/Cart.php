<?php

declare(strict_types=1);

namespace App\Modules\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Customer\Models\Customer;

final class Cart extends Model
{
    use SoftDeletes;

    public const ACTIVE = "active";
    public const CONVERTED = "converted";
    public const MERGED = "merged";

    protected $fillable = [
        "uuid",
        "customer_id",
        "guest_token",
        "status",
        "currency_code",
        "coupon_code",
        "subtotal",
        "discount_amount",
        "tax_amount",
        "shipping_amount",
        "shipping_rate_uuid",
        "shipping_method_uuid",
        "shipping_method_name",
        "shipping_method_code",
        "estimated_delivery_min_days",
        "estimated_delivery_max_days",
        "shipping_address",
        "grand_total",
        "expires_at",
    ];
    protected function casts(): array
    {
        return [
            "subtotal" => "decimal:2",
            "discount_amount" => "decimal:2",
            "tax_amount" => "decimal:2",
            "shipping_amount" => "decimal:2",
            "grand_total" => "decimal:2",
            "shipping_address" => "array",
            "estimated_delivery_min_days" => "integer",
            "estimated_delivery_max_days" => "integer",
            "expires_at" => "datetime",
        ];
    }
    protected static function booted(): void
    {
        static::creating(fn(Cart $m) => ($m->uuid ??= (string) Str::uuid()));
    }
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, "customer_id");
    }
}
