<?php

declare(strict_types=1);

namespace App\Modules\Category\Actions;

use App\Modules\Category\Services\CategoryService;

/**
 * List active categories for the public storefront catalog.
 *
 * @package App\Modules\Category\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class ListStorefrontCategoriesAction
{
    /**
     * Create a new list storefront categories action.
     *
     * @param CategoryService $service
     */
    public function __construct(
        private readonly CategoryService $service
    ) {
    }

    /**
     * Execute storefront category listing.
     *
     * @param array<string, mixed> $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function execute(array $filters)
    {
        return $this->service->storefrontList($filters);
    }
}