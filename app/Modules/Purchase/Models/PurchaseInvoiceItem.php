<?php
namespace App\Modules\Purchase\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class PurchaseInvoiceItem extends Model
{
    protected $fillable = [
        "uuid",
        "purchase_invoice_id",
        "purchase_order_item_id",
        "product_id",
        "product_variant_id",
        "unit_id",
        "sku",
        "description",
        "quantity",
        "unit_price",
        "discount_amount",
        "tax_rate",
        "tax_amount",
        "line_subtotal",
        "line_total",
    ];
    protected $casts = [
        "quantity" => "decimal:3",
        "unit_price" => "decimal:2",
        "discount_amount" => "decimal:2",
        "tax_rate" => "decimal:4",
        "tax_amount" => "decimal:2",
        "line_subtotal" => "decimal:2",
        "line_total" => "decimal:2",
    ];
    protected static function booted(): void
    {
        static::creating(fn(self $m) => ($m->uuid ??= (string) Str::uuid()));
    }
    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, "purchase_invoice_id");
    }
}
