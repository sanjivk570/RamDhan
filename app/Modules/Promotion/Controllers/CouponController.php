<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Promotion\Actions\ApplyCouponAction;
use App\Modules\Promotion\Actions\RemoveCouponAction;
use App\Modules\Promotion\Requests\ApplyCouponRequest;
use Illuminate\Http\Request;

/** Frontend cart coupon endpoints. */
final class CouponController extends Controller
{
    public function __construct(
        private readonly ApplyCouponAction $applyAction,
        private readonly RemoveCouponAction $removeAction,
    ) {}

    public function apply(ApplyCouponRequest $request)
    {
        return ApiResponse::success(
            $this->applyAction->execute(
                $request->user()?->id,
                $request->header('X-Guest-Token') ?: $request->input('guest_token'),
                $request->string('code')->toString(),
            ),
            'Coupon applied successfully.'
        );
    }

    public function remove(Request $request)
    {
        return ApiResponse::success(
            $this->removeAction->execute(
                $request->user()?->id,
                $request->header('X-Guest-Token') ?: $request->input('guest_token'),
            ),
            'Coupon removed successfully.'
        );
    }
}
