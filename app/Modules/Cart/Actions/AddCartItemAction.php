<?php

declare(strict_types=1);

namespace App\Modules\Cart\Actions;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Services\CartService;

/**
 * Application action for AddCartItemAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AddCartItemAction
{
    public function __construct(private readonly CartService $service) {}

    public function execute(Cart $cart, string $productUuid, ?string $variantUuid, float $quantity): Cart
    {
        return $this->service->add($cart, $productUuid, $variantUuid, $quantity);
    }
}
