<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Actions;

use App\Modules\Wishlist\Services\WishlistService;

/**
 * Application action for RemoveWishlistItemAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class RemoveWishlistItemAction
{
    public function __construct(private readonly WishlistService $service) {}

    public function execute(int $customerId, string $uuid): void
    {
        $this->service->remove($customerId, $uuid);
    }
}
