<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Promotion\Actions\AdminListCouponsAction;
use App\Modules\Promotion\Actions\CreateCouponAction;
use App\Modules\Promotion\Actions\DeleteCouponAction;
use App\Modules\Promotion\Actions\UpdateCouponAction;
use App\Modules\Promotion\Models\Coupon;
use App\Modules\Promotion\Resources\CouponResource;
use Illuminate\Http\Request;

/** Administrative coupon management endpoints. */
final class CouponController extends Controller
{
    public function __construct(
        private readonly AdminListCouponsAction $listAction,
        private readonly CreateCouponAction $createAction,
        private readonly UpdateCouponAction $updateAction,
        private readonly DeleteCouponAction $deleteAction,
    ) {}

    public function index(Request $request)
    {
        $coupons = $this->listAction->execute($request->all());

        return ApiResponse::paginated(
            $coupons,
            CouponResource::collection($coupons),
            'Coupons fetched successfully.'
        );
    }

    private function rules(bool $create = false): array
    {
        return [
            'code' => $create ? ['required', 'string', 'max:100', 'unique:coupons,code'] : ['sometimes', 'string', 'max:100'],
            'name' => [$create ? 'required' : 'sometimes', 'string', 'max:200'],
            'discount_type' => [$create ? 'required' : 'sometimes', 'in:percentage,fixed'],
            'discount_value' => [$create ? 'required' : 'sometimes', 'numeric', 'gt:0'],
            'maximum_discount' => ['nullable', 'numeric', 'gte:0'],
            'minimum_order_amount' => ['nullable', 'numeric', 'gte:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_customer_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function store(Request $request)
    {
        return ApiResponse::success(
            new CouponResource($this->createAction->execute($request->validate($this->rules(true)))),
            'Coupon created successfully.'
        );
    }

    public function update(Request $request, string $uuid)
    {
        $coupon = Coupon::where('uuid', $uuid)->firstOrFail();

        return ApiResponse::success(
            new CouponResource($this->updateAction->execute($coupon, $request->validate($this->rules()))),
            'Coupon updated successfully.'
        );
    }

    public function destroy(string $uuid)
    {
        $coupon = Coupon::where('uuid', $uuid)->firstOrFail();
        $this->deleteAction->execute($coupon);

        return ApiResponse::success([], 'Coupon deleted successfully.');
    }
}
