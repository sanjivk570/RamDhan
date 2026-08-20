<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Actions;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Services\CartService;

/**
 * Application action for RemoveCouponAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class RemoveCouponAction
{
    public function __construct(private readonly CartService $cartService) {}

    public function execute(?int $customerId, ?string $guestToken): Cart
    {
        $cart = $this->cartService->get($customerId, $guestToken);
        $cart->update(['coupon_code' => null, 'discount_amount' => 0, 'grand_total' => (float) $cart->subtotal + (float) $cart->tax_amount + (float) $cart->shipping_amount]);
        return $cart->fresh('items');
    }
}
