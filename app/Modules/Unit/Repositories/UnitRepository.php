<?php

declare(strict_types=1);

namespace App\Modules\Unit\Repositories;

use App\Modules\Unit\Models\Unit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UnitRepository
{
    /**
     * Retrieve paginated units with filters.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Unit::query()

            /*
             * Global search.
             */
            ->when($filters["search"] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where("name", "LIKE", "%{$search}%")
                        ->orWhere("code", "LIKE", "%{$search}%")
                        ->orWhere("symbol", "LIKE", "%{$search}%");
                });
            })

            /*
             * Column filters.
             */
            ->when(!empty($filters["filters"]["name"]), function ($query) use (
                $filters
            ) {
                $query->where(
                    "name",
                    "LIKE",
                    "%" . $filters["filters"]["name"] . "%"
                );
            })

            ->when(!empty($filters["filters"]["code"]), function ($query) use (
                $filters
            ) {
                $query->where(
                    "code",
                    "LIKE",
                    "%" . $filters["filters"]["code"] . "%"
                );
            })

            ->when(!empty($filters["filters"]["symbol"]), function (
                $query
            ) use ($filters) {
                $query->where(
                    "symbol",
                    "LIKE",
                    "%" . $filters["filters"]["symbol"] . "%"
                );
            })

            ->when(
                isset($filters["filters"]["is_active"]) &&
                    $filters["filters"]["is_active"] !== "",
                function ($query) use ($filters) {
                    $query->where(
                        "is_active",
                        (bool) $filters["filters"]["is_active"]
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
     * Find a unit by UUID.
     */
    public function findByUuid(string $uuid): ?Unit
    {
        return Unit::where("uuid", $uuid)->first();
    }

    /**
     * Find a unit by UUID or fail.
     */
    public function findByUuidOrFail(string $uuid): Unit
    {
        return Unit::where("uuid", $uuid)->firstOrFail();
    }

    /**
     * Create a unit.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Unit
    {
        return Unit::create($data);
    }

    /**
     * Update a unit.
     *
     * @param array<string, mixed> $data
     */
    public function update(Unit $unit, array $data): Unit
    {
        $unit->update($data);

        return $unit->refresh();
    }

    /**
     * Change unit status.
     */
    public function changeStatus(Unit $unit, bool $status): Unit
    {
        $unit->update([
            "is_active" => $status,
        ]);

        return $unit->refresh();
    }

    /**
     * Soft delete a unit.
     */
    public function delete(Unit $unit): bool
    {
        return (bool) $unit->delete();
    }

    /**
     * Restore a unit.
     */
    public function restore(string $uuid): Unit
    {
        $unit = Unit::withTrashed()
            ->where("uuid", $uuid)
            ->firstOrFail();

        $unit->restore();

        return $unit->refresh();
    }
}
