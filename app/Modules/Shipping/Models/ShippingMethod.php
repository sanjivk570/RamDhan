<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ShippingMethod extends Model
{
    use SoftDeletes;

    protected $table = "shipping_methods";

    protected $fillable = [
        "uuid",
        "name",
        "code",
        "description",
        "min_delivery_days",
        "max_delivery_days",
        "is_active",
        "sort_order",
    ];

    protected function casts(): array
    {
        return [
            "min_delivery_days" => "integer",
            "max_delivery_days" => "integer",
            "is_active" => "boolean",
            "sort_order" => "integer",
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ShippingMethod $method) {
            if (empty($method->uuid)) {
                $method->uuid = (string) Str::uuid();
            }
        });
    }

    public function rates()
    {
        return $this->hasMany(ShippingRate::class, "shipping_method_id");
    }
}
