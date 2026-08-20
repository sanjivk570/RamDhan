<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Actions;

use App\Modules\Wishlist\Models\Wishlist;
use App\Modules\Wishlist\Services\WishlistService;

/**
 * Application action for AddWishlistItemAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AddWishlistItemAction
{
    public function __construct(private readonly WishlistService $service) {}

    public function execute(int $customerId, string $productUuid, ?string $variantUuid): Wishlist
    {
        return $this->service->add($customerId, $productUuid, $variantUuid);
    }
}
