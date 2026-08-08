<?php

declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Product\Actions\ChangeStatusAction;
use App\Modules\Product\Actions\CreateProductAction;
use App\Modules\Product\Actions\DeleteProductAction;
use App\Modules\Product\Actions\ListProductAction;
use App\Modules\Product\Actions\RestoreProductAction;
use App\Modules\Product\Actions\ShowProductAction;
use App\Modules\Product\Actions\UpdateProductAction;
use App\Modules\Product\Requests\ChangeStatusRequest;
use App\Modules\Product\Requests\CreateProductRequest;
use App\Modules\Product\Requests\ProductListRequest;
use App\Modules\Product\Requests\UpdateProductRequest;
use App\Modules\Product\Resources\ProductResource;

/**
 * Controller responsible for product management operations.
 *
 * Handles product listing, retrieval, creation, updating,
 * status changes, deletion, and restoration.
 *
 * @package App\Modules\Product\Controllers
 * @author Sanjiv Kumar Kushwaha
 */
class ProductController extends Controller
{

    /**
     * ProductController constructor.
     *
     * @param ListProductAction $listProductAction
     * @param ShowProductAction $showProductAction
     * @param CreateProductAction $createProductAction
     * @param UpdateProductAction $updateProductAction
     * @param DeleteProductAction $deleteProductAction
     * @param RestoreProductAction $restoreProductAction
     * @param ChangeStatusAction $changeStatusAction
     */
    public function __construct(
        private readonly ListProductAction $listProductAction,
        private readonly ShowProductAction $showProductAction,
        private readonly CreateProductAction $createProductAction,
        private readonly UpdateProductAction $updateProductAction,
        private readonly DeleteProductAction $deleteProductAction,
        private readonly RestoreProductAction $restoreProductAction,
        private readonly ChangeStatusAction $changeStatusAction
    ) {
    }

    /**
     * Display a paginated listing of products.
     *
     * @param ProductListRequest $request
     * @return JsonResponse
     */
    public function index(ProductListRequest $request)
    {
        $products = $this->listProductAction->execute($request->validated());

        return ApiResponse::paginated(
            $products,
            ProductResource::collection($products),
            'Products fetched successfully.'
        );
    }

    /**
     * Display the specified product.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function show(string $uuid)
    {
        $product = $this->showProductAction->execute($uuid);

        return ApiResponse::success(
            new ProductResource($product),
            'Product fetched successfully.'
        );
    }

    /**
     * Store a newly created product.
     *
     * @param CreateProductRequest $request
     * @return JsonResponse
     */
    public function store(CreateProductRequest $request)
    {
        $product = $this->createProductAction->execute($request->validated());

        return ApiResponse::success(
            new ProductResource($product),
            'Product created successfully.'
        );
    }

    /**
     * Update the specified product.
     *
     * @param UpdateProductRequest $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProductRequest $request, string $uuid) {
        $product = $this->updateProductAction->execute($uuid, $request->validated());

        return ApiResponse::success(
            new ProductResource($product),
            'Product updated successfully.'
        );
    }

    /**
     * Change the status of the specified product.
     *
     * @param ChangeStatusRequest $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(ChangeStatusRequest $request, string $uuid) {
        $product = $this->changeStatusAction->execute($uuid, $request->boolean('status'));
        return ApiResponse::success(
            new ProductResource($product),
            'Product status updated successfully.'
        );
    }

    /**
     * Soft delete the specified product.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $uuid)
    {
        $this->deleteProductAction->execute($uuid);

        return ApiResponse::success(
            [],
            'Product deleted successfully.'
        );
    }

    /**
     * Restore the specified product.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(string $uuid)
    {
        $product = $this->restoreProductAction->execute($uuid);

        return ApiResponse::success(
            new ProductResource($product),
            'Product restored successfully.'
        );
    }

    
}