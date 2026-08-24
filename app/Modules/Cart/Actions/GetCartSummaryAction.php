<?php

declare(strict_types=1);

namespace App\Modules\Cart\Actions;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Services\CartService;

/**
 * Application action for the recalculated cart summary.
 *
 * Returns the cart with every price recomputed (destination-aware tax,
 * discount, applied shipping and grand total) so the frontend always
 * renders totals that match the server.
 *
 * @package App\Modules\Cart\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class GetCartSummaryAction
{
    /**
     * Create a new get cart summary action.
     *
     * @param CartService $service
     */
    public function __construct(private readonly CartService $service) {}

    /**
     * Execute the summary recalculation.
     *
     * @param int|null $customerId
     * @param string|null $guestToken
     * @return Cart
     */
    public function execute(?int $customerId, ?string $guestToken): Cart
    {
        return $this->service->summary($customerId, $guestToken);
    }
}