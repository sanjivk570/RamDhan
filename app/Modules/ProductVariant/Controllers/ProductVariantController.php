<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Controllers;

use App\Http\Controllers\Controller;
use App\Core\Responses\ApiResponse;
use App\Modules\ProductVariant\Actions\ListProductVariantAction;
use App\Modules\ProductVariant\Actions\ShowProductVariantAction;
use App\Modules\ProductVariant\Actions\CreateProductVariantAction;
use App\Modules\ProductVariant\Actions\UpdateProductVariantAction;
use App\Modules\ProductVariant\Actions\DeleteProductVariantAction;
use App\Modules\ProductVariant\Actions\SetDefaultProductVariantAction;
use App\Modules\ProductVariant\Requests\ProductVariantListRequest;
use App\Modules\ProductVariant\Requests\CreateProductVariantRequest;
use App\Modules\ProductVariant\Requests\UpdateProductVariantRequest;
use App\Modules\ProductVariant\Resources\ProductVariantResource;

class ProductVariantController extends Controller
{
    public function __construct(
        private readonly ListProductVariantAction $listProductVariantAction,
        private readonly ShowProductVariantAction $showProductVariantAction,
        private readonly CreateProductVariantAction $createProductVariantAction,
        private readonly UpdateProductVariantAction $updateProductVariantAction,
        private readonly DeleteProductVariantAction $deleteProductVariantAction,
        private readonly SetDefaultProductVariantAction $setDefaultProductVariantAction
    ) {
    }

    public function index(
        ProductVariantListRequest $request,
        string $productUuid
    ) {
        $variants = $this->listProductVariantAction->execute(
            $productUuid,
            $request->validated()
        );

        return ApiResponse::paginated(
            $variants,
            ProductVariantResource::collection($variants),
            "Product variants fetched successfully."
        );
    }

    public function show(string $productUuid, string $variantUuid)
    {
        $variant = $this->showProductVariantAction->execute(
            $productUuid,
            $variantUuid
        );

        return ApiResponse::success(
            new ProductVariantResource($variant),
            "Product variant fetched successfully."
        );
    }

    public function store(
        CreateProductVariantRequest $request,
        string $productUuid
    ) {
        $variant = $this->createProductVariantAction->execute(
            $productUuid,
            $request->validated()
        );

        return ApiResponse::success(
            new ProductVariantResource($variant),
            "Product variant created successfully."
        );
    }

    public function update(
        UpdateProductVariantRequest $request,
        string $productUuid,
        string $variantUuid
    ) {
        $variant = $this->updateProductVariantAction->execute(
            $productUuid,
            $variantUuid,
            $request->validated()
        );

        return ApiResponse::success(
            new ProductVariantResource($variant),
            "Product variant updated successfully."
        );
    }

    public function destroy(string $productUuid, string $variantUuid)
    {
        $this->deleteProductVariantAction->execute($productUuid, $variantUuid);

        return ApiResponse::success(
            [],
            "Product variant deleted successfully."
        );
    }

    public function setDefault(string $productUuid, string $variantUuid)
    {
        $variant = $this->setDefaultProductVariantAction->execute(
            $productUuid,
            $variantUuid
        );

        return ApiResponse::success(
            new ProductVariantResource($variant),
            "Product variant set as default successfully."
        );
    }
}
