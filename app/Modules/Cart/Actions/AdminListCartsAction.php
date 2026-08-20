<?php

declare(strict_types=1);

namespace App\Modules\Cart\Actions;

use App\Modules\Cart\Models\Cart;

/**
 * Application action for AdminListCartsAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminListCartsAction
{
    public function execute(array $filters)
    {
        return Cart::with('items')
            ->when($filters['customer_id'] ?? null, fn ($q, $v) => $q->where('customer_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate($filters['per_page'] ?? 20);
    }
}
