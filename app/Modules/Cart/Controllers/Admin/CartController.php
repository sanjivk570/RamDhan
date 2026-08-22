<?php

declare(strict_types=1);

namespace App\Modules\Cart\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Cart\Actions\AdminListCartsAction;
use App\Modules\Cart\Actions\AdminShowCartAction;
use App\Modules\Cart\Requests\CartListRequest;
use App\Modules\Cart\Resources\CartResource;

/** Administrative cart inspection endpoints. */
final class CartController extends Controller
{
    public function __construct(
        private readonly AdminListCartsAction $listCartsAction,
        private readonly AdminShowCartAction $showCartAction,
    ) {}

    public function index(CartListRequest $request)
    {
        $carts = $this->listCartsAction->execute($request->validated());

        return ApiResponse::paginated(
            $carts,
            CartResource::collection($carts),
            'Carts fetched successfully.'
        );
    }

    public function show(string $uuid)
    {
        return ApiResponse::success(
            new CartResource($this->showCartAction->execute($uuid)),
            'Cart fetched successfully.'
        );
    }
}
