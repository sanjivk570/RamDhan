<?php

declare(strict_types=1);

namespace App\Modules\Cart\Actions;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Cart\Services\CartService;

/**
 * Application action for UpdateCartItemAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class UpdateCartItemAction
{
    public function __construct(private readonly CartService $service) {}

    public function execute(Cart $cart, CartItem $item, float $quantity): Cart
    {
        return $this->service->update($cart, $item, $quantity);
    }
}
