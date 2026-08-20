<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Actions;

use App\Modules\Wishlist\Models\Wishlist;

/**
 * Application action for AdminListWishlistAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminListWishlistAction
{
    public function execute(array $filters)
    {
        return Wishlist::query()
            ->when(
                $filters["customer_id"] ?? null,
                fn($q, $v) => $q->where("customer_id", $v)
            )
            ->latest()
            ->paginate($filters["per_page"] ?? 20);
    }
}
