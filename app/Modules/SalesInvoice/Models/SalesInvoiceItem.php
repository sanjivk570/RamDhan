<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
final class SalesInvoiceItem extends Model
{
    protected $fillable = [
        "uuid",
        "sales_invoice_id",
        "order_item_id",
        "product_id",
        "product_variant_id",
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
    protected function casts(): array
    {
        return [
            "quantity" => "decimal:3",
            "unit_price" => "decimal:2",
            "discount_amount" => "decimal:2",
            "tax_rate" => "decimal:4",
            "tax_amount" => "decimal:2",
            "line_subtotal" => "decimal:2",
            "line_total" => "decimal:2",
        ];
    }
    protected static function booted(): void
    {
        static::creating(
            fn(SalesInvoiceItem $m) => ($m->uuid ??= (string) Str::uuid())
        );
    }
}
