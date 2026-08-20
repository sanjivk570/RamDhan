<?php

declare(strict_types=1);

namespace App\Modules\Cart\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariant;

final class CartItem extends Model
{
    protected $fillable = [
        "uuid",
        "cart_id",
        "product_id",
        "tax_class_id",
        "product_variant_id",
        "sku",
        "product_name",
        "variant_name",
        "quantity",
        "unit_price",
        "compare_price",
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
            "compare_price" => "decimal:2",
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
            fn(CartItem $m) => ($m->uuid ??= (string) Str::uuid())
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
