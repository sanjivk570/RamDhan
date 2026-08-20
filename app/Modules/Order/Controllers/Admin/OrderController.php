<?php

declare(strict_types=1);

namespace App\Modules\Order\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Order\Actions\AdminListOrdersAction;
use App\Modules\Order\Actions\AdminShowOrderAction;
use App\Modules\Order\Actions\ChangeOrderStatusAction;
use App\Modules\Order\Requests\ChangeOrderStatusRequest;
use App\Modules\Order\Requests\OrderListRequest;
use App\Modules\Order\Resources\OrderResource;

/** Administrative order management endpoints. */
final class OrderController extends Controller
{
    public function __construct(
        private readonly AdminListOrdersAction $listAction,
        private readonly AdminShowOrderAction $showAction,
        private readonly ChangeOrderStatusAction $statusAction,
    ) {}

    public function index(OrderListRequest $request)
    {
        $orders = $this->listAction->execute($request->validated());

        return ApiResponse::paginated(
            $orders,
            OrderResource::collection($orders),
            'Orders fetched successfully.'
        );
    }

    public function show(string $uuid)
    {
        return ApiResponse::success(
            new OrderResource($this->showAction->execute($uuid)),
            'Order fetched successfully.'
        );
    }

    public function status(ChangeOrderStatusRequest $request, string $uuid)
    {
        $order = $this->showAction->execute($uuid);

        return ApiResponse::success(
            new OrderResource($this->statusAction->execute(
                $order,
                $request->string('status')->toString(),
                $request->input('note'),
                $request->user()->id,
            )),
            'Order status updated successfully.'
        );
    }
}
