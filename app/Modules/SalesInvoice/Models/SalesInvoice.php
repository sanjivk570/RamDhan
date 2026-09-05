<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Modules\Order\Models\Order;
use App\Modules\Customer\Models\Customer;
final class SalesInvoice extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "uuid",
        "invoice_number",
        "order_id",
        "customer_id",
        "status",
        "invoice_date",
        "due_date",
        "currency_code",
        "subtotal",
        "discount_amount",
        "tax_amount",
        "shipping_amount",
        "grand_total",
        "paid_amount",
        "due_amount",
        "billing_address",
    ];
    protected function casts(): array
    {
        return [
            "invoice_date" => "date",
            "due_date" => "date",
            "billing_address" => "array",
            "subtotal" => "decimal:2",
            "discount_amount" => "decimal:2",
            "tax_amount" => "decimal:2",
            "shipping_amount" => "decimal:2",
            "grand_total" => "decimal:2",
            "paid_amount" => "decimal:2",
            "due_amount" => "decimal:2",
        ];
    }
    protected static function booted(): void
    {
        static::creating(function (SalesInvoice $m) {
            $m->uuid ??= (string) Str::uuid();
            $m->invoice_number ??=
                "INV-" .
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
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
