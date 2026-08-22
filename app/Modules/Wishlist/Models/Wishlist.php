<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Modules\Customer\Models\Customer;
use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariant;

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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
