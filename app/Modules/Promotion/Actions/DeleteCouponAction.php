<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Actions;

use App\Modules\Promotion\Models\Coupon;

/**
 * Application action for DeleteCouponAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class DeleteCouponAction
{
    public function execute(Coupon $coupon): void
    {
        $coupon->delete();
    }
}
