<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
final class Wishlist extends Model
{
    protected $fillable = [
        "uuid",
        "customer_id",
        "product_id",
        "product_variant_id",
    ];
    protected static function booted(): void
    {
        static::creating(
            fn(Wishlist $m) => ($m->uuid ??= (string) Str::uuid())
        );
    }
}
