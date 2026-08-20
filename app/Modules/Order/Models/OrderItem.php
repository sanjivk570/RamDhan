<?php

declare(strict_types=1);

namespace App\Modules\Order\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
final class OrderItem extends Model
{
    protected $fillable = [
        "uuid",
        "order_id",
        "product_id",
        "product_variant_id",
        "sku",
        "product_name",
        "variant_name",
        "quantity",
        "unit_price",
        "discount_amount",
        "tax_rate",
        "tax_amount",
        "line_subtotal",
        "line_total",
    ];
    protected function casts(): array
    {
        return [
            "quantity" => "decimal:3",
            "unit_price" => "decimal:2",
            "discount_amount" => "decimal:2",
            "tax_rate" => "decimal:4",
            "tax_amount" => "decimal:2",
            "line_subtotal" => "decimal:2",
            "line_total" => "decimal:2",
        ];
    }
    protected static function booted(): void
    {
        static::creating(
            fn(OrderItem $m) => ($m->uuid ??= (string) Str::uuid())
        );
    }
}
