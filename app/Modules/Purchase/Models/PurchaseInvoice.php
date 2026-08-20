<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PurchaseInvoice extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "uuid",
        "invoice_number",
        "supplier_id",
        "purchase_order_id",
        "goods_receipt_id",
        "created_by",
        "status",
        "invoice_date",
        "due_date",
        "supplier_invoice_number",
        "currency_code",
        "subtotal",
        "discount_amount",
        "tax_amount",
        "shipping_amount",
        "grand_total",
        "paid_amount",
        "due_amount",
        "notes",
        "posted_at",
    ];
    protected $casts = [
        "invoice_date" => "date",
        "due_date" => "date",
        "posted_at" => "datetime",
        "subtotal" => "decimal:2",
        "discount_amount" => "decimal:2",
        "tax_amount" => "decimal:2",
        "shipping_amount" => "decimal:2",
        "grand_total" => "decimal:2",
        "paid_amount" => "decimal:2",
        "due_amount" => "decimal:2",
    ];
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            $m->uuid ??= (string) Str::uuid();
            $m->invoice_number ??=
                "PINV-" .
                now()->format("YmdHis") .
                "-" .
                Str::upper(Str::random(4));
        });
    }
    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }
    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }
}
