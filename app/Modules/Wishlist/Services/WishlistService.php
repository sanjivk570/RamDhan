<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Services;
use App\Modules\Wishlist\Models\Wishlist;
use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariant;
use Illuminate\Support\Str;
final class WishlistService
{
    public function list(int $customerId)
    {
        return Wishlist::where("customer_id", $customerId)
            ->latest()
            ->paginate(20);
    }
    public function add(
        int $customerId,
        string $productUuid,
        ?string $variantUuid
    ): Wishlist {
        $p = Product::where("uuid", $productUuid)
            ->where("is_active", true)
            ->firstOrFail();
        $v = $variantUuid
            ? ProductVariant::where("uuid", $variantUuid)
                ->where("product_id", $p->id)
                ->firstOrFail()
            : null;
        return Wishlist::firstOrCreate(
            [
                "customer_id" => $customerId,
                "product_id" => $p->id,
                "product_variant_id" => $v?->id,
            ],
            ["uuid" => (string) Str::uuid()]
        );
    }
    public function remove(int $customerId, string $uuid): void
    {
        Wishlist::where("customer_id", $customerId)
            ->where("uuid", $uuid)
            ->firstOrFail()
            ->delete();
    }
}
