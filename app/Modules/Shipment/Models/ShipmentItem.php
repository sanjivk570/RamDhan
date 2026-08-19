<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
final class ShipmentItem extends Model
{
    protected $fillable = [
        "uuid",
        "shipment_id",
        "order_item_id",
        "product_id",
        "product_variant_id",
        "quantity",
    ];
    protected function casts(): array
    {
        return ["quantity" => "decimal:3"];
    }
    protected static function booted(): void
    {
        static::creating(
            fn(ShipmentItem $m) => ($m->uuid ??= (string) Str::uuid())
        );
    }
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
