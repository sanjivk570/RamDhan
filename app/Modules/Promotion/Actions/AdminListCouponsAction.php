<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Actions;

use App\Modules\Promotion\Models\Coupon;

/**
 * Application action for AdminListCouponsAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminListCouponsAction
{
    public function execute(array $filters)
    {
        return Coupon::query()
            ->when(
                $filters["search"] ?? null,
                fn($q, $v) => $q->where(
                    fn($z) => $z
                        ->where("code", "like", "%" . $v . "%")
                        ->orWhere("name", "like", "%" . $v . "%")
                )
            )
            ->when(
                array_key_exists("status", $filters) &&
                    $filters["status"] !== null,
                fn($q) => $q->where("is_active", (bool) $filters["status"])
            )
            ->latest()
            ->paginate($filters["per_page"] ?? 20);
    }
}
