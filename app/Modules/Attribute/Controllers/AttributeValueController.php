<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Controllers;

use App\Http\Controllers\Controller;
use App\Core\Responses\ApiResponse;
use App\Modules\Attribute\Actions\CreateAttributeValueAction;
use App\Modules\Attribute\Actions\DeleteAttributeValueAction;
use App\Modules\Attribute\Actions\RestoreAttributeValueAction;
use App\Modules\Attribute\Actions\ShowAttributeValueAction;
use App\Modules\Attribute\Actions\UpdateAttributeValueAction;
use App\Modules\Attribute\Requests\CreateAttributeValueRequest;
use App\Modules\Attribute\Requests\UpdateAttributeValueRequest;
use App\Modules\Attribute\Resources\AttributeValueResource;

class AttributeValueController extends Controller
{
    public function __construct(
        private readonly CreateAttributeValueAction $createAttributeValueAction,
        private readonly ShowAttributeValueAction $showAttributeValueAction,
        private readonly UpdateAttributeValueAction $updateAttributeValueAction,
        private readonly DeleteAttributeValueAction $deleteAttributeValueAction,
        private readonly RestoreAttributeValueAction $restoreAttributeValueAction
    ) {
    }

    /**
     * Show attribute value.
     */
    public function show(string $attributeUuid, string $valueUuid)
    {
        $value = $this->showAttributeValueAction->execute(
            $attributeUuid,
            $valueUuid
        );

        return ApiResponse::success(
            new AttributeValueResource($value),
            "Attribute value fetched successfully."
        );
    }

    /**
     * Create attribute value.
     */
    public function store(
        CreateAttributeValueRequest $request,
        string $attributeUuid
    ) {
        $value = $this->createAttributeValueAction->execute(
            $attributeUuid,
            $request->validated()
        );

        return ApiResponse::success(
            new AttributeValueResource($value),
            "Attribute value created successfully."
        );
    }

    /**
     * Update attribute value.
     */
    public function update(
        UpdateAttributeValueRequest $request,
        string $attributeUuid,
        string $valueUuid
    ) {
        $value = $this->updateAttributeValueAction->execute(
            $attributeUuid,
            $valueUuid,
            $request->validated()
        );

        return ApiResponse::success(
            new AttributeValueResource($value),
            "Attribute value updated successfully."
        );
    }

    /**
     * Delete attribute value.
     */
    public function destroy(string $attributeUuid, string $valueUuid)
    {
        $this->deleteAttributeValueAction->execute($attributeUuid, $valueUuid);

        return ApiResponse::success(
            [],
            "Attribute value deleted successfully."
        );
    }

    /**
     * Restore attribute value.
     */
    public function restore(string $attributeUuid, string $valueUuid)
    {
        $value = $this->restoreAttributeValueAction->execute(
            $attributeUuid,
            $valueUuid
        );

        return ApiResponse::success(
            new AttributeValueResource($value),
            "Attribute value restored successfully."
        );
    }
}
