<?php

declare(strict_types=1);

namespace App\Modules\Cart\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Cart\Actions\AddCartItemAction;
use App\Modules\Cart\Actions\GetCartAction;
use App\Modules\Cart\Actions\GetCartSummaryAction;
use App\Modules\Cart\Actions\MergeCartAction;
use App\Modules\Cart\Actions\RemoveCartItemAction;
use App\Modules\Cart\Actions\UpdateCartItemAction;
use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Requests\AddCartItemRequest;
use App\Modules\Cart\Requests\ApplyShippingRequest;
use App\Modules\Cart\Requests\UpdateCartItemRequest;
use App\Modules\Cart\Resources\CartResource;
use App\Modules\Cart\Resources\CartSummaryResource;
use App\Modules\Shipping\Actions\ApplyCartShippingAction;
use Illuminate\Http\Request;

/**
 * Customer/guest cart API controller.
 *
 * Controllers contain transport concerns only; cart business rules live in
 * Actions and the Cart service.
 */
final class CartController extends Controller
{
    public function __construct(
        private readonly GetCartAction $getCartAction,
        private readonly AddCartItemAction $addCartItemAction,
        private readonly UpdateCartItemAction $updateCartItemAction,
        private readonly RemoveCartItemAction $removeCartItemAction,
        private readonly MergeCartAction $mergeCartAction,
        private readonly GetCartSummaryAction $getCartSummaryAction,
        private readonly ApplyCartShippingAction $applyCartShippingAction,
    ) {}

    /**
     * Resolve the acting identity (customer or guest) for this request.
     *
     * @return array{0: int|null, 1: string|null} [customerId, guestToken]
     */
    private function resolveIdentity(Request $request): array
    {
        $customerId = !empty($request->user()?->id)
            ? (int) $request->user()->id
            : (auth('customer')->user()?->id !== null
                ? (int) auth('customer')->user()->id
                : null);

        return [
            $customerId,
            $request->header('X-Guest-Token') ?: ($request->input('guest_token') ?: null),
        ];
    }

    private function resolveCart(Request $request): Cart
    {
        [$customerId, $guestToken] = $this->resolveIdentity($request);

        return $this->getCartAction->execute($customerId, $guestToken);
    }

    public function show(Request $request)
    {
        return ApiResponse::success(
            $this->resolveCart($request),
            'Cart fetched successfully.'
        );
    }

    public function add(AddCartItemRequest $request)
    {
        $cart = $this->resolveCart($request);

        return ApiResponse::success(
            $this->addCartItemAction->execute(
                $cart,
                $request->string('product_uuid')->toString(),
                $request->input('variant_uuid'),
                (float) $request->input('quantity'),
            ),
            'Item added to cart.'
        );
    }

    public function update(UpdateCartItemRequest $request, string $uuid)
    {
        $cart = $this->resolveCart($request);
        $item = $cart->items()->where('uuid', $uuid)->firstOrFail();

        return ApiResponse::success(
            $this->updateCartItemAction->execute(
                $cart,
                $item,
                (float) $request->input('quantity'),
            ),
            'Cart updated successfully.'
        );
    }

    public function remove(Request $request, string $uuid)
    {
        $cart = $this->resolveCart($request);
        $item = $cart->items()->where('uuid', $uuid)->firstOrFail();

        return ApiResponse::success(
            $this->removeCartItemAction->execute($cart, $item),
            'Cart item removed successfully.'
        );
    }

    public function merge(Request $request)
    {
        [$customerId] = $this->resolveIdentity($request);

        if ($customerId === null) {
            return ApiResponse::error('A logged-in customer is required to merge carts.', null, 401);
        }

        $guestToken = $request->header('X-Guest-Token') ?: $request->input('guest_token');

        if (!$guestToken) {
            return ApiResponse::error('Guest token is required.');
        }

        $guestCart = Cart::where('guest_token', $guestToken)
            ->where('status', 'active')
            ->firstOrFail();

        $customerCart = $this->getCartAction->execute($customerId, null);

        return ApiResponse::success(
            $this->mergeCartAction->execute($customerCart, $guestCart),
            'Guest cart merged successfully.'
        );
    }

    /**
     * Fully recalculated cart summary.
     *
     * GET /api/v1/cart/summary
     *
     * Recomputes destination-aware tax, keeps the applied coupon,
     * shipping selection and grand total consistent after every change.
     */
    public function summary(Request $request)
    {
        [$customerId, $guestToken] = $this->resolveIdentity($request);

        $cart = $this->getCartSummaryAction->execute($customerId, $guestToken);

        return ApiResponse::success(
            new CartSummaryResource($cart),
            'Cart summary fetched successfully.'
        );
    }

    /**
     * Apply the shopper-selected shipping rate to the cart.
     *
     * POST /api/v1/cart/shipping-method
     *
     * Called after address selection; validates the rate against the
     * destination server-side, stores it and recalculates all prices.
     */
    public function applyShipping(ApplyShippingRequest $request)
    {
        [$customerId, $guestToken] = $this->resolveIdentity($request);

        $cart = $this->resolveCart($request);

        try {
            $updated = $this->applyCartShippingAction->execute(
                $cart->uuid,
                $request->string('shipping_rate_uuid')->toString(),
                $customerId,
                $guestToken,
                $request->input('customer_address_uuid'),
                [
                    'country_code' => $request->input('country_code') ?? $request->input('country'),
                    'state_code' => $request->input('state_code') ?? $request->input('state'),
                    'postal_code' => $request->input('postal_code'),
                ],
            );
        } catch (\RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), null, 422);
        }

        return ApiResponse::success(
            new CartSummaryResource($updated),
            'Shipping method applied successfully.'
        );
    }
}
