<?php

declare(strict_types=1);

namespace App\Modules\Product\Controllers\Storefront;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Product\Actions\ListStorefrontProductsAction;
use App\Modules\Product\Actions\ShowStorefrontProductAction;
use App\Modules\Product\Requests\StorefrontProductListRequest;
use App\Modules\Product\Resources\ProductResource;

/**
 * Storefront (public) product catalog controller.
 *
 * Handles public product listing and retrieval. Only active
 * (published) products are exposed.
 *
 * @package App\Modules\Product\Controllers\Storefront
 * @author Sanjiv Kumar Kushwaha
 */
class StorefrontProductController extends Controller
{
    /**
     * Create a new storefront product controller instance.
     *
     * @param ListStorefrontProductsAction $listAction
     * @param ShowStorefrontProductAction $showAction
     */
    public function __construct(
        private readonly ListStorefrontProductsAction $listAction,
        private readonly ShowStorefrontProductAction $showAction
    ) {
    }

    /**
     * Display a paginated listing of active products.
     *
     * @param StorefrontProductListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(StorefrontProductListRequest $request)
    {
        $products = $this->listAction->execute(
            $request->validated()
        );

        return ApiResponse::paginated(
            $products,
            ProductResource::collection($products),
            'Products fetched successfully.'
        );
    }

    /**
     * Display the specified active product.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $uuid)
    {
        $product = $this->showAction->execute($uuid);

        return ApiResponse::success(
            new ProductResource($product),
            'Product fetched successfully.'
        );
    }
}