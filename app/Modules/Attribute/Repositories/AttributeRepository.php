<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Repositories;

use App\Modules\Attribute\Models\Attribute;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AttributeRepository
{
    /**
     * Retrieve paginated attributes.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Attribute::query()
            ->withCount("values")

            ->when($filters["search"] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where("name", "LIKE", "%{$search}%")->orWhere(
                        "slug",
                        "LIKE",
                        "%{$search}%"
                    );
                });
            })

            ->when(
                isset($filters["filters"]["name"]) &&
                    $filters["filters"]["name"] !== "",
                function ($query) use ($filters) {
                    $query->where(
                        "name",
                        "LIKE",
                        "%" . $filters["filters"]["name"] . "%"
                    );
                }
            )

            ->when(
                isset($filters["filters"]["type"]) &&
                    $filters["filters"]["type"] !== "",
                function ($query) use ($filters) {
                    $query->where("type", $filters["filters"]["type"]);
                }
            )

            ->when(
                isset($filters["filters"]["status"]) &&
                    $filters["filters"]["status"] !== "",
                function ($query) use ($filters) {
                    $query->where(
                        "is_active",
                        (bool) $filters["filters"]["status"]
                    );
                }
            )

            ->orderBy(
                $filters["sort_by"] ?? "sort_order",
                $filters["sort_order"] ?? "asc"
            )

            ->paginate($filters["per_page"] ?? 20);
    }

    /**
     * Find attribute by UUID.
     */
    public function findByUuid(string $uuid): ?Attribute
    {
        return Attribute::query()
            ->with("values")
            ->where("uuid", $uuid)
            ->first();
    }

    /**
     * Find attribute or fail.
     */
    public function findByUuidOrFail(string $uuid): Attribute
    {
        return Attribute::query()
            ->with("values")
            ->where("uuid", $uuid)
            ->firstOrFail();
    }

    /**
     * Create attribute.
     */
    public function create(array $data): Attribute
    {
        return Attribute::create($data);
    }

    /**
     * Update attribute.
     */
    public function update(Attribute $attribute, array $data): Attribute
    {
        $attribute->update($data);

        return $attribute->refresh();
    }

    /**
     * Delete attribute.
     */
    public function delete(Attribute $attribute): bool
    {
        return (bool) $attribute->delete();
    }

    /**
     * Restore attribute.
     */
    public function restore(string $uuid): Attribute
    {
        $attribute = Attribute::withTrashed()
            ->where("uuid", $uuid)
            ->firstOrFail();

        $attribute->restore();

        return $attribute->refresh();
    }
}
