<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Services\CartService;

/**
 * Application action for CheckoutPreviewAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class CheckoutPreviewAction
{
    public function __construct(private readonly CartService $cartService) {}

    public function execute(?int $customerId, ?string $guestToken): Cart
    {
        return $this->cartService->get($customerId, $guestToken)->load('items');
    }
}
