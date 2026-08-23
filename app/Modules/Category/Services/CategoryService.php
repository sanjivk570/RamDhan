<?php

declare(strict_types=1);

namespace App\Modules\Category\Services;

use App\Modules\Category\Models\Category;
use App\Modules\Category\Repositories\CategoryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use RuntimeException;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $repository
    ) {
    }

    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {

        return $this->repository->paginate(
            $filters
        );
    }

    public function findByUuid(
        string $uuid
    ): ?Category {

        return $this->repository->findByUuid(
            $uuid
        );
    }

    /**
     * Retrieve paginated active categories for the storefront (public catalog).
     *
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator
     */
    public function storefrontList(
        array $filters = []
    ): LengthAwarePaginator {

        return $this->repository->paginateActive(
            $filters
        );
    }

    /**
     * Retrieve an active (published) category by UUID for the storefront.
     *
     * @param string $uuid
     * @return ?Category
     */
    public function storefrontDetails(
        string $uuid
    ): ?Category {

        return $this->repository->findActiveByUuid($uuid);
    }

    public function create(
        array $data
    ): Category {

        /*
         * Generate slug automatically
         * when it is not supplied.
         */
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug(
                $data['name']
            );
        } else {
            $data['slug'] = Str::slug(
                $data['slug']
            );
        }

        return $this->repository->create(
            $data
        );
    }

    public function update(
        Category $category,
        array $data
    ): Category {

        /*
         * Prevent category from becoming
         * its own parent.
         */
        if (
            array_key_exists('parent_id', $data)
            && $data['parent_id'] !== null
            && (int) $data['parent_id'] === $category->id
        ) {
            throw new RuntimeException(
                'A category cannot be its own parent.'
            );
        }

        if (
            array_key_exists('name', $data)
            && empty($data['slug'])
        ) {
            $data['slug'] = $this->generateUniqueSlug(
                $data['name'],
                $category->id
            );
        }

        if (
            array_key_exists('slug', $data)
            && $data['slug']
        ) {
            $data['slug'] = Str::slug(
                $data['slug']
            );
        }

        return $this->repository->update(
            $category,
            $data
        );
    }

    public function delete(
        Category $category
    ): bool {

        return $this->repository->delete(
            $category
        );
    }

    public function restore(
        Category $category
    ): bool {

        return $this->repository->restore(
            $category
        );
    }

    public function updateStatus(
        Category $category,
        bool $status
    ): Category {

        return $this->repository->updateStatus(
            $category,
            $status
        );
    }

    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {

        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'category';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            Category::query()
                ->where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) =>
                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        )
                )
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public function findByUuidWithTrashed(
        string $uuid
    ): ?Category {

        return $this->repository
            ->findByUuidWithTrashed($uuid);
    }
}