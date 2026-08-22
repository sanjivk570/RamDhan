<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Services;
use App\Modules\Promotion\Models\Coupon;
use App\Modules\Promotion\Repositories\CouponRepository;
use Illuminate\Support\Facades\DB;
use RuntimeException;
final class CouponService
{
    public function __construct(
        private readonly CouponRepository $repository
    ) {
    }

    /**
     * Retrieve a paginated list of coupons.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(array $filters)
    {
        return $this->repository->paginate($filters);
    }

    public function validate(
        string $code,
        float $subtotal,
        ?int $customerId = null
    ): Coupon {
        // Use lockForUpdate to prevent race conditions on coupon usage limits
        $c = Coupon::whereRaw("UPPER(code)=?", [strtoupper(trim($code))])
            ->where("is_active", true)
            ->whereNull("deleted_at") // Explicitly exclude soft-deleted coupons
            ->where(function ($q) {
                $q->whereNull("starts_at")->orWhere("starts_at", "<=", now());
            })
            ->where(function ($q) {
                $q->whereNull("ends_at")->orWhere("ends_at", ">=", now());
            })
            ->lockForUpdate()
            ->firstOrFail();

        if ($subtotal < (float) $c->minimum_order_amount) {
            throw new RuntimeException(
                "Minimum order amount for this coupon is " .
                    $c->minimum_order_amount
            );
        }

        // Check global usage limit
        if ($c->usage_limit !== null && $c->used_count >= $c->usage_limit) {
            throw new RuntimeException("Coupon usage limit reached.");
        }

        // Check per-customer limit
        if ($customerId && $c->per_customer_limit !== null) {
            $used = DB::table("coupon_redemptions")
                ->where("coupon_id", $c->id)
                ->where("customer_id", $customerId)
                ->count();
            if ($used >= $c->per_customer_limit) {
                throw new RuntimeException(
                    "Coupon usage limit reached for this customer."
                );
            }
        }

        return $c;
    }
    public function discount(Coupon $c, float $subtotal): float
    {
        $d =
            $c->discount_type === "percentage"
                ? ($subtotal * (float) $c->discount_value) / 100
                : (float) $c->discount_value;
        if ($c->maximum_discount !== null) {
            $d = min($d, (float) $c->maximum_discount);
        }
        return min($d, $subtotal);
    }
    public function redeem(
        Coupon $c,
        ?int $customerId,
        int $orderId,
        float $discount
    ): void {
        DB::transaction(function () use ($c, $customerId, $orderId, $discount) {
            DB::table("coupon_redemptions")->insert([
                "uuid" => (string) \Illuminate\Support\Str::uuid(),
                "coupon_id" => $c->id,
                "customer_id" => $customerId,
                "order_id" => $orderId,
                "discount_amount" => $discount,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
            Coupon::whereKey($c->id)
                ->lockForUpdate()
                ->increment("used_count");
        });
    }
}
