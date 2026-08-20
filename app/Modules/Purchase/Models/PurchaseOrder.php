<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Models;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

final class PurchaseOrder extends Model
{
    use SoftDeletes;

    public const DRAFT = "draft";
    public const SUBMITTED = "submitted";
    public const APPROVED = "approved";
    public const PARTIALLY_RECEIVED = "partially_received";
    public const RECEIVED = "received";
    public const CANCELLED = "cancelled";
    public const CLOSED = "closed";

    protected $fillable = [
        "uuid",
        "po_number",
        "supplier_id",
        "created_by",
        "approved_by",
        "status",
        "order_date",
        "expected_date",
        "payment_terms_days",
        "currency_code",
        "subtotal",
        "discount_amount",
        "tax_amount",
        "shipping_amount",
        "grand_total",
        "notes",
        "submitted_at",
        "approved_at",
        "cancelled_at",
        "cancelled_by",
        "cancellation_reason",
    ];

    protected function casts(): array
    {
        return [
            "order_date" => "date",
            "expected_date" => "date",
            "payment_terms_days" => "integer",
            "subtotal" => "decimal:2",
            "discount_amount" => "decimal:2",
            "tax_amount" => "decimal:2",
            "shipping_amount" => "decimal:2",
            "grand_total" => "decimal:2",
            "submitted_at" => "datetime",
            "approved_at" => "datetime",
            "cancelled_at" => "datetime",
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $order): void {
            $order->uuid ??= (string) Str::uuid();
            $order->po_number ??= self::nextNumber();
        });
    }

    private static function nextNumber(): string
    {
        return "PO-" .
            now()->format("Ym") .
            "-" .
            str_pad(
                (string) ((int) self::withTrashed()->max("id") + 1),
                6,
                "0",
                STR_PAD_LEFT
            );
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, "approved_by");
    }
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function isEditable(): bool
    {
        return $this->status === self::DRAFT;
    }
    public function isReceivable(): bool
    {
        return in_array(
            $this->status,
            [self::APPROVED, self::PARTIALLY_RECEIVED],
            true
        );
    }
}
