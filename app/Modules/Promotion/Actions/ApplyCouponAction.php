<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Actions;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Services\CartService;
use App\Modules\Promotion\Services\CouponService;

/**
 * Application action for ApplyCouponAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ApplyCouponAction
{
    public function __construct(private readonly CartService $cartService, private readonly CouponService $couponService) {}

    public function execute(?int $customerId, ?string $guestToken, string $code): Cart
    {
        $cart = $this->cartService->get($customerId, $guestToken)->load('items');
        $coupon = $this->couponService->validate($code, (float) $cart->subtotal, $customerId);
        $discount = $this->couponService->discount($coupon, (float) $cart->subtotal);
        $cart->update([
            'coupon_code' => $coupon->code,
            'discount_amount' => $discount,
            'grand_total' => max(0, (float) $cart->subtotal - $discount + (float) $cart->tax_amount + (float) $cart->shipping_amount),
        ]);
        return $cart->fresh('items');
    }
}
