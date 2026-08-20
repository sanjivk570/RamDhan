<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ShippingZone extends Model
{
    use SoftDeletes;

    protected $table = "shipping_zones";

    protected $fillable = [
        "uuid",
        "name",
        "code",
        "description",
        "countries",
        "states",
        "postal_codes",
        "is_active",
        "sort_order",
    ];

    protected function casts(): array
    {
        return [
            "countries" => "array",
            "states" => "array",
            "postal_codes" => "array",
            "is_active" => "boolean",
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ShippingZone $zone) {
            if (empty($zone->uuid)) {
                $zone->uuid = (string) Str::uuid();
            }
        });
    }

    public function rates()
    {
        return $this->hasMany(ShippingRate::class, "shipping_zone_id");
    }
}
