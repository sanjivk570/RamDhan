<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Models;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

final class GoodsReceipt extends Model
{
    use SoftDeletes;

    public const DRAFT = 'draft';
    public const POSTED = 'posted';
    public const VOID = 'void';

    protected $fillable = [
        'uuid','grn_number','purchase_order_id','supplier_id','received_by','posted_by','status',
        'receipt_date','supplier_document_date','supplier_document_number','notes','posted_at',
        'voided_at','voided_by','void_reason',
    ];

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
            'supplier_document_date' => 'date',
            'posted_at' => 'datetime',
            'voided_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (GoodsReceipt $grn): void {
            $grn->uuid ??= (string) Str::uuid();
            $grn->grn_number ??= 'GRN-' . now()->format('Ym') . '-' . str_pad((string) ((int) self::withTrashed()->max('id') + 1), 6, '0', STR_PAD_LEFT);
        });
    }

    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function receiver(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
    public function items(): HasMany { return $this->hasMany(GoodsReceiptItem::class); }
}
