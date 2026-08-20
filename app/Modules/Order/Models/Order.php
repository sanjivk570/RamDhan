<?php

declare(strict_types=1);

namespace App\Modules\Order\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
final class Order extends Model
{
    use SoftDeletes;
    public const PENDING = "pending";
    public const CONFIRMED = "confirmed";
    public const PROCESSING = "processing";
    public const SHIPPED = "shipped";
    public const DELIVERED = "delivered";
    public const CANCELLED = "cancelled";
    public const COMPLETED = "completed";
    protected $fillable = [
        "uuid",
        "order_number",
        "customer_id",
        "guest_token",
        "customer_email",
        "customer_name",
        "customer_phone",
        "status",
        "payment_status",
        "fulfillment_status",
        "currency_code",
        "subtotal",
        "discount_amount",
        "tax_amount",
        "shipping_amount",
        "grand_total",
        "coupon_code",
        "payment_method",
        "billing_address",
        "shipping_address",
        "customer_note",
        "internal_note",
        "placed_at",
        "cancelled_at",
        "cancelled_by",
        "cancellation_reason",
    ];
    protected function casts(): array
    {
        return [
            "billing_address" => "array",
            "shipping_address" => "array",
            "subtotal" => "decimal:2",
            "discount_amount" => "decimal:2",
            "tax_amount" => "decimal:2",
            "shipping_amount" => "decimal:2",
            "grand_total" => "decimal:2",
            "placed_at" => "datetime",
            "cancelled_at" => "datetime",
        ];
    }
    protected static function booted(): void
    {
        static::creating(function (Order $m) {
            $m->uuid ??= (string) Str::uuid();
            $m->order_number ??=
                "ORD-" .
                now()->format("Ym") .
                "-" .
                str_pad(
                    (string) ((int) self::withTrashed()->max("id") + 1),
                    6,
                    "0",
                    STR_PAD_LEFT
                );
        });
    }
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function histories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
