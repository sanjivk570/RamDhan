<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PurchaseOrderItem extends Model
{
    protected $fillable = [
        "uuid",
        "purchase_order_id",
        "product_id",
        "product_variant_id",
        "unit_id",
        "sku",
        "description",
        "ordered_quantity",
        "received_quantity",
        "rejected_quantity",
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
            "ordered_quantity" => "decimal:3",
            "received_quantity" => "decimal:3",
            "rejected_quantity" => "decimal:3",
            "unit_price" => "decimal:2",
            "discount_amount" => "decimal:2",
            "tax_rate" => "decimal:4",
            "tax_amount" => "decimal:2",
            "line_subtotal" => "decimal:2",
            "line_total" => "decimal:2",
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function remainingQuantity(): float
    {
        return max(
            0,
            (float) $this->ordered_quantity - (float) $this->received_quantity
        );
    }
}
