<?php

declare(strict_types=1);

namespace App\Modules\Cart\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Cart\Actions\AdminListCartsAction;
use App\Modules\Cart\Actions\AdminShowCartAction;
use App\Modules\Cart\Resources\CartResource;
use Illuminate\Http\Request;

/** Administrative cart inspection endpoints. */
final class CartController extends Controller
{
    public function __construct(
        private readonly AdminListCartsAction $listCartsAction,
        private readonly AdminShowCartAction $showCartAction,
    ) {}

    public function index(Request $request)
    {
        $carts = $this->listCartsAction->execute($request->all());

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
