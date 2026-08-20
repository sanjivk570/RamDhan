<?php

declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Order\Actions\CancelOrderAction;
use App\Modules\Order\Actions\CheckoutOrderAction;
use App\Modules\Order\Actions\CheckoutPreviewAction;
use App\Modules\Order\Actions\ListCustomerOrdersAction;
use App\Modules\Order\Actions\ReorderAction;
use App\Modules\Order\Actions\ShowCustomerOrderAction;
use App\Modules\Order\Actions\ShowGuestOrderAction;
use App\Modules\Order\Requests\CheckoutRequest;
use App\Modules\Order\Requests\OrderListRequest;
use App\Modules\Order\Resources\OrderResource;
use Illuminate\Http\Request;

/** Customer and guest order APIs. */
final class OrderController extends Controller
{
    public function __construct(
        private readonly CheckoutOrderAction $checkoutAction,
        private readonly CheckoutPreviewAction $previewAction,
        private readonly ListCustomerOrdersAction $listAction,
        private readonly ShowCustomerOrderAction $showAction,
        private readonly ShowGuestOrderAction $guestShowAction,
        private readonly CancelOrderAction $cancelAction,
        private readonly ReorderAction $reorderAction,
    ) {}

    public function checkout(CheckoutRequest $request)
    {
        $data = $request->validated();
        $data['guest_token'] = $data['guest_token'] ?? $request->header('X-Guest-Token');

        $customer = !empty($request->user()) ? $request->user() : auth('customer')->user();

        $order = $this->checkoutAction->execute(
            $data,
            $customer,
        );

        return ApiResponse::success(
            new OrderResource($order),
            'Order created successfully.'
        );
    }

    public function preview(Request $request)
    {
        $customerId = !empty($request->user()?->id) ? $request->user()?->id : auth('customer')->user()?->id;

        $cart = $this->previewAction->execute(
            $customerId,
            $request->header('X-Guest-Token') ?: $request->input('guest_token'),
        );

        return ApiResponse::success($cart, 'Checkout preview fetched successfully.');
    }

    public function index(OrderListRequest $request)
    {
        $customerId = !empty($request->user()?->id) ? $request->user()?->id : auth('customer')->user()?->id;
        $orders = $this->listAction->execute(
            $customerId,
            $request->validated(),
        );

        return ApiResponse::paginated(
            $orders,
            OrderResource::collection($orders),
            'Orders fetched successfully.'
        );
    }

    public function show(Request $request, string $uuid)
    {
        $customerId = !empty($request->user()?->id) ? $request->user()?->id : auth('customer')->user()?->id;
        return ApiResponse::success(
            new OrderResource($this->showAction->execute($customerId, $uuid)),
            'Order fetched successfully.'
        );
    }

    public function guestShow(Request $request, string $orderNumber)
    {
        $guestToken = $request->header('X-Guest-Token') ?: $request->input('guest_token');
        
        if (!$guestToken) {
            return ApiResponse::error('Guest token is required.', 401);
        }

        return ApiResponse::success(
            new OrderResource($this->guestShowAction->execute($orderNumber, $guestToken)),
            'Order fetched successfully.'
        );
    }

    public function cancel(Request $request, string $uuid)
    {
        $customerId = !empty($request->user()?->id) ? $request->user()?->id : auth('customer')->user()?->id;
        $order = $this->showAction->execute($customerId, $uuid);

        return ApiResponse::success(
            new OrderResource($this->cancelAction->execute($order, $request->input('reason'))),
            'Order cancelled successfully.'
        );
    }

    public function reorder(Request $request, string $uuid)
    {
        $customerId = !empty($request->user()?->id) ? $request->user()?->id : auth('customer')->user()?->id;
        $order = $this->showAction->execute($customerId, $uuid);

        return ApiResponse::success(
            $this->reorderAction->execute($order, $customerId),
            'Items added to cart successfully.'
        );
    }
}
