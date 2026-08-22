<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Actions;

use App\Modules\Promotion\Services\CouponService;

/**
 * Application action for AdminListCouponsAction.
 *
 * Keeps controllers as thin as possible and delegates the
 * actual query work to the coupon repository through the service.
 */
final class AdminListCouponsAction
{
    public function __construct(private readonly CouponService $service) {}

    public function execute(array $filters)
    {
        return $this->service->list($filters);
    }
}
