<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Models;

use App\Modules\Product\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $table = "inventory_transactions";

    /*
     * Transaction types.
     */
    public const TYPE_PURCHASE = "purchase";

    public const TYPE_SALE = "sale";

    public const TYPE_RETURN = "return";

    public const TYPE_ADJUSTMENT = "adjustment";

    public const TYPE_DAMAGE = "damage";

    public const TYPE_CANCELLATION = "cancellation";

    public const TYPE_TRANSFER = "transfer";

    protected $fillable = [
        "uuid",
        "inventory_stock_id",
        "product_id",
        "product_variant_id",
        "type",
        "quantity",
        "quantity_before",
        "quantity_after",
        "reference_type",
        "reference_id",
        "notes",
        "created_by",
    ];

    protected $casts = [
        "quantity" => "decimal:4",
        "quantity_before" => "decimal:4",
        "quantity_after" => "decimal:4",
    ];

    /**
     * Inventory stock.
     */
    public function inventoryStock(): BelongsTo
    {
        return $this->belongsTo(InventoryStock::class, "inventory_stock_id");
    }

    /**
     * Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id");
    }

    /**
     * Product.
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_variant_id");
    }

    /**
     * User who performed the transaction.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }
}
