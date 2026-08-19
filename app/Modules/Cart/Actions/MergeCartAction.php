<?php

declare(strict_types=1);

namespace App\Modules\Cart\Actions;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Services\CartService;

/**
 * Application action for MergeCartAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class MergeCartAction
{
    public function __construct(private readonly CartService $service) {}

    public function execute(Cart $customerCart, Cart $guestCart): Cart
    {
        return $this->service->merge($customerCart, $guestCart);
    }
}
