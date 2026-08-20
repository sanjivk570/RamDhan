<?php

declare(strict_types=1);

namespace App\Modules\Cart\Actions;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Cart\Services\CartService;

/**
 * Application action for RemoveCartItemAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class RemoveCartItemAction
{
    public function __construct(private readonly CartService $service) {}

    public function execute(Cart $cart, CartItem $item): Cart
    {
        return $this->service->remove($cart, $item);
    }
}
