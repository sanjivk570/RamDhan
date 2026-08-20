<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
final class SalesReturnItem extends Model
{
    protected $fillable = [
        "uuid",
        "sales_return_id",
        "order_item_id",
        "product_id",
        "product_variant_id",
        "quantity",
        "unit_price",
        "line_total",
        "reason",
    ];
    protected function casts(): array
    {
        return [
            "quantity" => "decimal:3",
            "unit_price" => "decimal:2",
            "line_total" => "decimal:2",
        ];
    }
    protected static function booted(): void
    {
        static::creating(
            fn(SalesReturnItem $m) => ($m->uuid ??= (string) Str::uuid())
        );
    }
}
