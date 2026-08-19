<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Actions;

use App\Modules\Promotion\Models\Coupon;

/**
 * Application action for UpdateCouponAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class UpdateCouponAction
{
    public function execute(Coupon $coupon, array $data): Coupon
    {
        $coupon->update($data);
        return $coupon->fresh();
    }
}
