<?php

declare(strict_types=1);

namespace App\Modules\Category\Actions;

use App\Modules\Category\Models\Category;
use App\Modules\Category\Services\CategoryService;

/**
 * Show an active category from the public storefront catalog.
 *
 * @package App\Modules\Category\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class ShowStorefrontCategoryAction
{
    /**
     * Create a new show storefront category action.
     *
     * @param CategoryService $service
     */
    public function __construct(
        private readonly CategoryService $service
    ) {
    }

    /**
     * Execute storefront category details retrieval.
     *
     * @param string $uuid
     * @return ?Category
     */
    public function execute(string $uuid): ?Category
    {
        return $this->service->storefrontDetails($uuid);
    }
}