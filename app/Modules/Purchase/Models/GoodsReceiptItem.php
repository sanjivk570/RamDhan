<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class GoodsReceiptItem extends Model
{
    protected $fillable = [
        'uuid','goods_receipt_id','purchase_order_item_id','product_id','product_variant_id',
        'ordered_quantity','previously_received_quantity','received_quantity','accepted_quantity',
        'rejected_quantity','unit_cost','batch_number','expiry_date','notes',
    ];

    protected function casts(): array
    {
        return [
            'ordered_quantity' => 'decimal:3',
            'previously_received_quantity' => 'decimal:3',
            'received_quantity' => 'decimal:3',
            'accepted_quantity' => 'decimal:3',
            'rejected_quantity' => 'decimal:3',
            'unit_cost' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }

    public function goodsReceipt(): BelongsTo { return $this->belongsTo(GoodsReceipt::class); }
    public function purchaseOrderItem(): BelongsTo { return $this->belongsTo(PurchaseOrderItem::class); }
}
