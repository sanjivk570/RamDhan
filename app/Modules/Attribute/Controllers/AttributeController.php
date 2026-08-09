<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Controllers;

use App\Http\Controllers\Controller;
use App\Core\Responses\ApiResponse;
use App\Modules\Attribute\Actions\CreateAttributeAction;
use App\Modules\Attribute\Actions\DeleteAttributeAction;
use App\Modules\Attribute\Actions\ListAttributeAction;
use App\Modules\Attribute\Actions\RestoreAttributeAction;
use App\Modules\Attribute\Actions\ShowAttributeAction;
use App\Modules\Attribute\Actions\UpdateAttributeAction;
use App\Modules\Attribute\Requests\AttributeListRequest;
use App\Modules\Attribute\Requests\CreateAttributeRequest;
use App\Modules\Attribute\Requests\UpdateAttributeRequest;
use App\Modules\Attribute\Resources\AttributeResource;

class AttributeController extends Controller
{
    public function __construct(
        private readonly ListAttributeAction $listAttributeAction,
        private readonly ShowAttributeAction $showAttributeAction,
        private readonly CreateAttributeAction $createAttributeAction,
        private readonly UpdateAttributeAction $updateAttributeAction,
        private readonly DeleteAttributeAction $deleteAttributeAction,
        private readonly RestoreAttributeAction $restoreAttributeAction
    ) {
    }

    /**
     * List attributes.
     */
    public function index(AttributeListRequest $request)
    {
        $attributes = $this->listAttributeAction->execute(
            $request->validated()
        );

        return ApiResponse::paginated(
            $attributes,
            AttributeResource::collection($attributes),
            "Attributes fetched successfully."
        );
    }

    /**
     * Show attribute.
     */
    public function show(string $uuid)
    {
        $attribute = $this->showAttributeAction->execute($uuid);

        return ApiResponse::success(
            new AttributeResource($attribute),
            "Attribute fetched successfully."
        );
    }

    /**
     * Create attribute.
     */
    public function store(CreateAttributeRequest $request)
    {
        $attribute = $this->createAttributeAction->execute(
            $request->validated()
        );

        return ApiResponse::success(
            new AttributeResource($attribute),
            "Attribute created successfully."
        );
    }

    /**
     * Update attribute.
     */
    public function update(UpdateAttributeRequest $request, string $uuid)
    {
        $attribute = $this->updateAttributeAction->execute(
            $uuid,
            $request->validated()
        );

        return ApiResponse::success(
            new AttributeResource($attribute),
            "Attribute updated successfully."
        );
    }

    /**
     * Delete attribute.
     */
    public function destroy(string $uuid)
    {
        $this->deleteAttributeAction->execute($uuid);

        return ApiResponse::success([], "Attribute deleted successfully.");
    }

    /**
     * Restore attribute.
     */
    public function restore(string $uuid)
    {
        $attribute = $this->restoreAttributeAction->execute($uuid);

        return ApiResponse::success(
            new AttributeResource($attribute),
            "Attribute restored successfully."
        );
    }
}
