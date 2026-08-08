<?php

declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Product\Actions\CreateProductImageAction;
use App\Modules\Product\Actions\DeleteProductImageAction;
use App\Modules\Product\Actions\SetPrimaryProductImageAction;
use App\Modules\Product\Requests\CreateProductImageRequest;
use App\Modules\Product\Requests\SetPrimaryProductImageRequest;
use App\Modules\Product\Resources\ProductImageResource;
use App\Modules\Product\Actions\ForceDeleteProductImageAction;

class ProductImageController extends Controller
{
    public function __construct(
        private readonly CreateProductImageAction $createProductImageAction,
        private readonly DeleteProductImageAction $deleteProductImageAction,
        private readonly SetPrimaryProductImageAction $setPrimaryProductImageAction,
        private readonly ForceDeleteProductImageAction $forceDeleteProductImageAction
    ) {
    }

    /**
     * Store a new product image.
     *
     * POST /products/{uuid}/images
     */
    public function store(
        CreateProductImageRequest $request,
        string $uuid
    ) {
        $image = $this->createProductImageAction->execute(
            $uuid,
            $request->validated()
        );

        return ApiResponse::success(
            new ProductImageResource($image),
            'Product image uploaded successfully.'
        );
    }

    /**
     * Delete a product image.
     *
     * DELETE /products/{uuid}/images/{imageUuid}
     */
    public function destroy(
        string $uuid,
        string $imageUuid
    ) {
        $this->deleteProductImageAction->execute(
            $uuid,
            $imageUuid
        );

        return ApiResponse::success(
            [],
            'Product image deleted successfully.'
        );
    }

    /**
     * Set product image as primary.
     *
     * PATCH /products/{uuid}/images/{imageUuid}/primary
     */
    public function setPrimary(
        SetPrimaryProductImageRequest $request,
        string $uuid,
        string $imageUuid
    ) {
        $image = $this->setPrimaryProductImageAction->execute(
            $uuid,
            $imageUuid
        );

        return ApiResponse::success(
            new ProductImageResource($image),
            'Product primary image updated successfully.'
        );
    }

    public function forceDestroy(string $uuid, string $imageUuid)
    {
        $this->forceDeleteProductImageAction->execute(
            $uuid, $imageUuid
        );

        return ApiResponse::success(
            [],
            'Product image permanently deleted successfully.'
        );
    }
}