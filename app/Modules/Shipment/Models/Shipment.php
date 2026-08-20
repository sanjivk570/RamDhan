<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
final class Shipment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "uuid",
        "shipment_number",
        "order_id",
        "status",
        "carrier",
        "service",
        "tracking_number",
        "tracking_url",
        "shipping_address",
        "shipped_at",
        "delivered_at",
        "cancelled_at",
        "created_by",
        "notes",
    ];
    protected function casts(): array
    {
        return [
            "shipping_address" => "array",
            "shipped_at" => "datetime",
            "delivered_at" => "datetime",
            "cancelled_at" => "datetime",
        ];
    }
    protected static function booted(): void
    {
        static::creating(function (Shipment $m) {
            $m->uuid ??= (string) Str::uuid();
            $m->shipment_number ??=
                "SHP-" .
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
        return $this->hasMany(ShipmentItem::class);
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Order\Models\Order::class);
    }
}
