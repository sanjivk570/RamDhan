<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Wishlist\Actions\AddWishlistItemAction;
use App\Modules\Wishlist\Actions\ListWishlistAction;
use App\Modules\Wishlist\Actions\RemoveWishlistItemAction;
use Illuminate\Http\Request;

/** Customer wishlist endpoints. */
final class WishlistController extends Controller
{
    public function __construct(
        private readonly ListWishlistAction $listAction,
        private readonly AddWishlistItemAction $addAction,
        private readonly RemoveWishlistItemAction $removeAction,
    ) {}

    public function index(Request $request)
    {
        return ApiResponse::success(
            $this->listAction->execute($request->user()->id),
            'Wishlist fetched successfully.'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_uuid' => ['required', 'uuid'],
            'variant_uuid' => ['nullable', 'uuid'],
        ]);

        return ApiResponse::success(
            $this->addAction->execute(
                $request->user()->id,
                $data['product_uuid'],
                $data['variant_uuid'] ?? null,
            ),
            'Added to wishlist successfully.'
        );
    }

    public function destroy(Request $request, string $uuid)
    {
        $this->removeAction->execute($request->user()->id, $uuid);

        return ApiResponse::success([], 'Removed from wishlist successfully.');
    }
}
