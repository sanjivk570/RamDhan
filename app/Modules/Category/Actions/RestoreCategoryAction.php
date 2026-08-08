<?php

declare(strict_types=1);

namespace App\Modules\Category\Actions;

use App\Modules\Category\Models\Category;
use App\Modules\Category\Services\CategoryService;

class RestoreCategoryAction
{
    public function __construct(
        private readonly CategoryService $service
    ) {
    }

    public function execute(
        Category $category
    ): bool {

        return $this->service->restore(
            $category
        );
    }
}