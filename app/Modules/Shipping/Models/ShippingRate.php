<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ShippingRate extends Model
{
    use SoftDeletes;

    protected $table = "shipping_rates";

    protected $fillable = [
        "uuid",
        "shipping_zone_id",
        "shipping_method_id",
        "min_weight",
        "max_weight",
        "min_order_amount",
        "max_order_amount",
        "base_rate",
        "per_kg_rate",
        "free_shipping_threshold",
        "is_active",
        "sort_order",
    ];

    protected function casts(): array
    {
        return [
            "min_weight" => "decimal:3",
            "max_weight" => "decimal:3",
            "min_order_amount" => "decimal:2",
            "max_order_amount" => "decimal:2",
            "base_rate" => "decimal:2",
            "per_kg_rate" => "decimal:2",
            "free_shipping_threshold" => "decimal:2",
            "is_active" => "boolean",
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ShippingRate $rate) {
            if (empty($rate->uuid)) {
                $rate->uuid = (string) Str::uuid();
            }
        });
    }

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, "shipping_zone_id");
    }

    public function method()
    {
        return $this->belongsTo(ShippingMethod::class, "shipping_method_id");
    }
}
