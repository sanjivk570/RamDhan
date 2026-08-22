<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Services;
use App\Modules\Wishlist\Models\Wishlist;
use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariant;
use Illuminate\Support\Str;
final class WishlistService
{
    public function __construct(
        private readonly \App\Modules\Wishlist\Repositories\WishlistRepository $repository
    ) {
    }

    public function listAdmin(array $filters)
    {
        return $this->repository->paginate($filters);
    }

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
        // Ensure product is active and not soft-deleted
        $p = Product::where("uuid", $productUuid)
            ->where("is_active", true)
            ->whereNull("deleted_at")
            ->firstOrFail();

        // If variant specified, verify it exists and belongs to product
        $v = $variantUuid
            ? ProductVariant::where("uuid", $variantUuid)
                ->where("product_id", $p->id)
                ->firstOrFail()
            : null;

        // Prevent race conditions by using explicit locking
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
