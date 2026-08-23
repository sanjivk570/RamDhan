<?php

namespace App\Modules\Category\Repositories;

use App\Modules\Category\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {

        $query = Category::query()
            ->with('parent', 'media');

        if (!empty($filters['search'])) {

            $search = $filters['search'];

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'slug',
                    'like',
                    "%{$search}%"
                );
            });
        }

        /*
         * Filters
         *
         * Example:
         * filters[name]=Mobile
         * filters[status]=1
         * filters[parent_id]=2
         */
        $filterValues = $filters['filters'] ?? [];

        /*
         * Category Name
         */
        if (
            isset($filterValues['name'])
            && $filterValues['name'] !== ''
        ) {

            $query->where(
                'name',
                'like',
                '%' . $filterValues['name'] . '%'
            );
        }

        /*
         * Category slug
         */
        if (
            isset($filterValues['slug'])
            && $filterValues['slug'] !== ''
        ) {

            $query->where(
                'slug',
                'like',
                '%' . $filterValues['slug'] . '%'
            );
        }

        /*
         * Category description
         */
        if (
            isset($filterValues['description'])
            && $filterValues['description'] !== ''
        ) {

            $query->where(
                'description',
                'like',
                '%' . $filterValues['description'] . '%'
            );
        }

        /*
         * Category Status
         */
        if (
            isset($filterValues['status'])
            && $filterValues['status'] !== ''
        ) {

            $query->where(
                'is_active',
                (bool) $filterValues['status']
            );
        }

        /*
         * Parent Category
         *
         * null / empty parent can be handled
         * separately if required.
         */
        if (
            isset($filterValues['parent_id'])
            && $filterValues['parent_id'] !== ''
        ) {

            $query->where(
                'parent_id',
                $filterValues['parent_id']
            );
        }

        /*
        * Parent Category Name
        */
        if (
            isset($filterValues['parent_name'])
            && $filterValues['parent_name'] !== ''
        ) {

            $query->whereHas('parent', function ($q) use ($filterValues) {
                $q->where(
                    'name',
                    'like',
                    '%' . $filterValues['parent_name'] . '%'
                );
            });
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';

        $sortOrder = $filters['sort_order'] ?? 'desc';

        $allowedSorts = [
            'name',
            'slug',
            'is_active',
            'sort_order',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        $query->orderBy(
            $sortBy,
            $sortOrder
        );

        return $query->paginate(
            $filters['per_page'] ?? 15
        );
    }

    public function findByUuid(
        string $uuid
    ): ?Category {

        return Category::query()
            ->with([
                'parent',
                'children',
                'media',
            ])
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * Retrieve paginated active categories for the storefront.
     *
     * Only categories that are published (is_active = true) are returned.
     *
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator
     */
    public function paginateActive(
        array $filters = []
    ): LengthAwarePaginator {

        $query = Category::query()
            ->with('parent', 'children', 'media')
            // Storefront only exposes active categories.
            ->where('is_active', true);

        if (!empty($filters['search'])) {

            $search = $filters['search'];

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'slug',
                    'like',
                    "%{$search}%"
                );
            });
        }

        $sortBy = $filters['sort_by'] ?? 'sort_order';

        $sortOrder = $filters['sort_order'] ?? 'asc';

        $allowedSorts = [
            'name',
            'slug',
            'sort_order',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'sort_order';
        }

        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'asc';
        }

        $query->orderBy(
            $sortBy,
            $sortOrder
        );

        return $query->paginate(
            $filters['per_page'] ?? 15
        );
    }

    /**
     * Find an active (published) category by UUID for the storefront.
     *
     * @param string $uuid
     * @return ?Category
     */
    public function findActiveByUuid(
        string $uuid
    ): ?Category {

        return Category::query()
            ->with([
                'parent',
                'children',
                'media',
            ])
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->first();
    }

    public function create(
        array $data
    ): Category {

        return Category::create($data);
    }

    public function update(
        Category $category,
        array $data
    ): Category {

        $category->update($data);

        return $category->fresh([
            'parent',
            'children',
        ]);
    }

    public function delete(
        Category $category
    ): bool {

        return (bool) $category->delete();
    }

    public function restore(
        Category $category
    ): bool {

        return (bool) $category->restore();
    }

    public function findByUuidWithTrashed(
        string $uuid
    ): ?Category {

        return Category::withTrashed()
            ->with([
                'parent',
                'children',
                'media',
            ])
            ->where('uuid', $uuid)
            ->first();
    }

    public function updateStatus(
        Category $category,
        bool $status
    ): Category {

        $category->update([
            'is_active' => $status,
        ]);

        return $category->fresh();
    }
}