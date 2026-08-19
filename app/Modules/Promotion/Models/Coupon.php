<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
final class Coupon extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "uuid",
        "code",
        "name",
        "discount_type",
        "discount_value",
        "maximum_discount",
        "minimum_order_amount",
        "usage_limit",
        "per_customer_limit",
        "used_count",
        "starts_at",
        "ends_at",
        "is_active",
    ];
    protected function casts(): array
    {
        return [
            "discount_value" => "decimal:2",
            "maximum_discount" => "decimal:2",
            "minimum_order_amount" => "decimal:2",
            "starts_at" => "datetime",
            "ends_at" => "datetime",
            "is_active" => "boolean",
        ];
    }
    protected static function booted(): void
    {
        static::creating(fn(Coupon $m) => ($m->uuid ??= (string) Str::uuid()));
    }
}
