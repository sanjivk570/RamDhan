<?php

declare(strict_types=1);

namespace App\Modules\Cart\Actions;

use App\Modules\Cart\Models\Cart;

/**
 * Application action for AdminShowCartAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminShowCartAction
{
    public function execute(string $uuid): Cart
    {
        return Cart::with('items')->where('uuid', $uuid)->firstOrFail();
    }
}
