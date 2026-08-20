<?php

declare(strict_types=1);

namespace App\Modules\Category\Controllers;

use App\Core\Responses\ApiResponse;
use App\Modules\Category\Actions\CreateCategoryAction;
use App\Modules\Category\Actions\DeleteCategoryAction;
use App\Modules\Category\Actions\RestoreCategoryAction;
use App\Modules\Category\Actions\UpdateCategoryAction;
use App\Modules\Category\Requests\CategoryListRequest;
use App\Modules\Category\Requests\CreateCategoryRequest;
use App\Modules\Category\Requests\UpdateCategoryRequest;
use App\Modules\Category\Requests\UpdateCategoryStatusRequest;
use App\Modules\Category\Resources\CategoryResource;
use App\Modules\Category\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CategoryController
{
    public function __construct(
        private readonly CategoryService $service
    ) {
    }

    /**
     * Category list.
     */
    public function index(
        CategoryListRequest $request
    ): JsonResponse {

        $paginator = $this->service->paginate(
            $request->validated()
        );

        return ApiResponse::paginated(
            $paginator,
            CategoryResource::collection(
                $paginator->items()
            ),
            'Categories fetched successfully.'
        );
    }

    /**
     * Category details.
     */
    public function show(
        string $uuid
    ): JsonResponse {

        $category = $this->service->findByUuid(
            $uuid
        );

        if (!$category) {
            return ApiResponse::notFound(
                'Category not found.'
            );
        }

        return ApiResponse::success(
            new CategoryResource($category),
            'Category fetched successfully.'
        );
    }

    /**
     * Create category.
     */
    public function store(
        CreateCategoryRequest $request,
        CreateCategoryAction $action
    ): JsonResponse {

        $category = $action->execute(
            $request->validated()
        );

        return ApiResponse::created(
            new CategoryResource($category),
            'Category created successfully.'
        );
    }

    /**
     * Update category.
     */
    public function update(
        UpdateCategoryRequest $request,
        string $uuid,
        UpdateCategoryAction $action
    ): JsonResponse {

        $category = $this->service->findByUuid(
            $uuid
        );

        if (!$category) {
            return ApiResponse::notFound(
                'Category not found.'
            );
        }

        $category = $action->execute(
            $category,
            $request->validated()
        );

        return ApiResponse::updated(
            new CategoryResource($category),
            'Category updated successfully.'
        );
    }

    /**
     * Update status.
     */
    public function status(
        UpdateCategoryStatusRequest $request,
        string $uuid
    ): JsonResponse {

        $category = $this->service->findByUuid(
            $uuid
        );

        if (!$category) {
            return ApiResponse::notFound(
                'Category not found.'
            );
        }

        $category = $this->service->updateStatus(
            $category,
            $request->boolean('status')
        );

        return ApiResponse::updated(
            new CategoryResource($category),
            'Category status updated successfully.'
        );
    }

    /**
     * Soft delete.
     */
    public function destroy(
        string $uuid
    ): JsonResponse {

        $category = $this->service->findByUuid(
            $uuid
        );

        if (!$category) {
            return ApiResponse::notFound(
                'Category not found.'
            );
        }

        $this->service->delete(
            $category
        );

        return ApiResponse::deleted(
            'Category deleted successfully.'
        );
    }

    /**
     * Restore category.
     */
    public function restore(
        string $uuid
    ): JsonResponse {

        $category = $this->service
            ->findByUuidWithTrashed($uuid);

        if (!$category) {
            return ApiResponse::notFound(
                'Category not found.'
            );
        }

        if (!$category->trashed()) {
            return ApiResponse::error(
                'Category is already active.',
                null,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->service->restore($category);

        return ApiResponse::success(
            null,
            'Category restored successfully.'
        );
    }
}