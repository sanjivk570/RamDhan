<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Models;

use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariantAttributeValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "product_variants";

    protected $fillable = [
        "uuid",
        "product_id",
        "name",
        "sku",
        "price",
        "compare_price",
        "cost_price",
        "stock_quantity",
        "low_stock_threshold",
        "is_default",
        "is_active",
        "sort_order",
    ];

    protected $casts = [
        "price" => "decimal:2",
        "compare_price" => "decimal:2",
        "cost_price" => "decimal:2",
        "stock_quantity" => "integer",
        "low_stock_threshold" => "integer",
        "is_default" => "boolean",
        "is_active" => "boolean",
        "sort_order" => "integer",
    ];

    protected static function booted(): void
    {
        static::creating(function (self $variant): void {
            if (empty($variant->uuid)) {
                $variant->uuid = (string) Str::uuid();
            }
        });
    }

    /*
     * Parent product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id");
    }

    /*
     * Selected attribute values.
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(
            ProductVariantAttributeValue::class,
            "product_variant_id"
        );
    }

    /*
     * Active variants.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where("is_active", true);
    }

    /*
     * Default variant.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where("is_default", true);
    }
}
