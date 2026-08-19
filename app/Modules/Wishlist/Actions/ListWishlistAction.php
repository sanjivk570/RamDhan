<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Actions;

use App\Modules\Wishlist\Services\WishlistService;

/**
 * Application action for ListWishlistAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ListWishlistAction
{
    public function __construct(private readonly WishlistService $service) {}

    public function execute(int $customerId)
    {
        return $this->service->list($customerId);
    }
}
