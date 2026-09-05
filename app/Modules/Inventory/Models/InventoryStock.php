<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Models;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Modules\ProductVariant\Models\ProductVariant;

class InventoryStock extends Model
{
    use HasFactory;
    //use SoftDeletes;

    protected $table = "inventory_stocks";

    protected $fillable = [
        "uuid",
        "product_id",
        'product_variant_id',
        "quantity",
        "reserved_quantity",
        "low_stock_threshold",
        "is_active",
    ];

    protected $casts = [
        "quantity" => "decimal:4",
        "reserved_quantity" => "decimal:4",
        "low_stock_threshold" => "decimal:4",
        "is_active" => "boolean",
    ];

    /**
     * Product associated with this inventory stock.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id");
    }

    /**
     * Inventory transaction history.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(
            InventoryTransaction::class,
            "inventory_stock_id"
        );
    }

    protected static function booted()
    {
        static::creating(function ($stock) {
            if (empty($stock->uuid)) {
                $stock->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get available quantity.
     *
     * Available stock =
     * quantity - reserved_quantity
     */
    public function getAvailableQuantityAttribute(): string
    {
        return number_format(
            max(0, (float) $this->quantity - (float) $this->reserved_quantity),
            4,
            ".",
            ""
        );
    }

    /**
     * Determine whether stock is low.
     */
    public function isLowStock(): bool
    {
        if ($this->low_stock_threshold === null) {
            return false;
        }

        return (float) $this->available_quantity <=
            (float) $this->low_stock_threshold;
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }
}
