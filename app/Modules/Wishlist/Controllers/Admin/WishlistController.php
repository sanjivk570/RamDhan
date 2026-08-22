<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Wishlist\Actions\AdminListWishlistAction;
use App\Modules\Wishlist\Models\Wishlist;
use App\Modules\Wishlist\Requests\WishlistListRequest;
use App\Modules\Wishlist\Resources\WishlistResource;

/** Administrative wishlist inspection endpoints. */
final class WishlistController extends Controller
{
    public function __construct(private readonly AdminListWishlistAction $listAction) {}

    public function index(WishlistListRequest $request)
    {
        $records = $this->listAction->execute($request->validated());

        return ApiResponse::paginated(
            $records,
            WishlistResource::collection($records),
            'Wishlist records fetched successfully.'
        );
    }
}
