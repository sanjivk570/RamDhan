<?php
namespace App\Modules\Purchase\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class PurchaseReturnItem extends Model
{
    protected $fillable = [
        "uuid",
        "purchase_return_id",
        "product_id",
        "product_variant_id",
        "quantity",
        "unit_cost",
        "line_total",
        "reason",
    ];
    protected $casts = [
        "quantity" => "decimal:3",
        "unit_cost" => "decimal:2",
        "line_total" => "decimal:2",
    ];
    protected static function booted(): void
    {
        static::creating(fn(self $m) => ($m->uuid ??= (string) Str::uuid()));
    }
}
