<?php

declare(strict_types=1);

namespace App\Modules\Cart\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Cart\Actions\AddCartItemAction;
use App\Modules\Cart\Actions\GetCartAction;
use App\Modules\Cart\Actions\MergeCartAction;
use App\Modules\Cart\Actions\RemoveCartItemAction;
use App\Modules\Cart\Actions\UpdateCartItemAction;
use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Requests\AddCartItemRequest;
use App\Modules\Cart\Requests\UpdateCartItemRequest;
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
    ) {}

    private function resolveCart(Request $request): Cart
    {
        $customerId = !empty($request->user()?->id) ? $request->user()?->id : auth('customer')->user()?->id;
        return $this->getCartAction->execute(
            $customerId,
            $request->header('X-Guest-Token') ?: $request->input('guest_token'),
        );
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
        $guestToken = $request->header('X-Guest-Token') ?: $request->input('guest_token');

        if (!$guestToken) {
            return ApiResponse::error('Guest token is required.');
        }

        $guestCart = Cart::where('guest_token', $guestToken)
            ->where('status', 'active')
            ->firstOrFail();

        $customerCart = $this->getCartAction->execute($request->user()->id, null);

        return ApiResponse::success(
            $this->mergeCartAction->execute($customerCart, $guestCart),
            'Guest cart merged successfully.'
        );
    }
}
