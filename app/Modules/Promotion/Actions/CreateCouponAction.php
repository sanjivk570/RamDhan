<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Actions;

use App\Modules\Promotion\Models\Coupon;
use Illuminate\Support\Str;

/**
 * Application action for CreateCouponAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class CreateCouponAction
{
    public function execute(array $data): Coupon
    {
        $data['uuid'] = (string) Str::uuid();
        return Coupon::create($data);
    }
}
