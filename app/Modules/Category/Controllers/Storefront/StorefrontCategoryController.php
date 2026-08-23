<?php

declare(strict_types=1);

namespace App\Modules\Category\Controllers\Storefront;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Category\Actions\ListStorefrontCategoriesAction;
use App\Modules\Category\Actions\ShowStorefrontCategoryAction;
use App\Modules\Category\Requests\StorefrontCategoryListRequest;
use App\Modules\Category\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;

/**
 * Storefront (public) category catalog controller.
 *
 * Handles public category listing and retrieval. Only active
 * (published) categories are exposed.
 *
 * @package App\Modules\Category\Controllers\Storefront
 * @author Sanjiv Kumar Kushwaha
 */
class StorefrontCategoryController extends Controller
{
    /**
     * Create a new storefront category controller instance.
     *
     * @param ListStorefrontCategoriesAction $listAction
     * @param ShowStorefrontCategoryAction $showAction
     */
    public function __construct(
        private readonly ListStorefrontCategoriesAction $listAction,
        private readonly ShowStorefrontCategoryAction $showAction
    ) {
    }

    /**
     * Display a paginated listing of active categories.
     *
     * @param StorefrontCategoryListRequest $request
     * @return JsonResponse
     */
    public function index(StorefrontCategoryListRequest $request): JsonResponse
    {
        $categories = $this->listAction->execute(
            $request->validated()
        );

        return ApiResponse::paginated(
            $categories,
            CategoryResource::collection($categories),
            'Categories fetched successfully.'
        );
    }

    /**
     * Display the specified active category.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        $category = $this->showAction->execute($uuid);

        if (!$category) {
            return ApiResponse::notFound('Category not found.');
        }

        return ApiResponse::success(
            new CategoryResource($category),
            'Category fetched successfully.'
        );
    }
}