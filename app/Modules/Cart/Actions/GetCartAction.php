<?php

declare(strict_types=1);

namespace App\Modules\Cart\Actions;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Services\CartService;

/**
 * Application action for GetCartAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class GetCartAction
{
    public function __construct(private readonly CartService $service) {}

    public function execute(?int $customerId, ?string $guestToken): Cart
    {
        return $this->service->get($customerId, $guestToken);
    }
}
