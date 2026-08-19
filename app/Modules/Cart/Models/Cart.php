<?php

declare(strict_types=1);

namespace App\Modules\Cart\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
final class Cart extends Model
{
    use SoftDeletes;
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
}
