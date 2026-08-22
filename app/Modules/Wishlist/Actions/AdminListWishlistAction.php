<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Actions;

use App\Modules\Wishlist\Services\WishlistService;

/**
 * Application action for AdminListWishlistAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminListWishlistAction
{
    public function __construct(private readonly WishlistService $service) {}

    public function execute(array $filters)
    {
        return $this->service->listAdmin($filters);
    }
}
