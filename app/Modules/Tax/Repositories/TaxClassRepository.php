<?php

declare(strict_types=1);

namespace App\Modules\Tax\Repositories;

use App\Modules\Tax\Models\TaxClass;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaxClassRepository
{
    /**
     * Retrieve paginated tax classes.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return TaxClass::query()

            ->withCount("rates")

            ->when($filters["search"] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where("name", "LIKE", "%{$search}%")->orWhere(
                        "code",
                        "LIKE",
                        "%{$search}%"
                    );
                });
            })

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

    public function findByUuid(string $uuid): ?TaxClass
    {
        return TaxClass::where("uuid", $uuid)->first();
    }

    public function findByUuidOrFail(string $uuid): TaxClass
    {
        return TaxClass::with("rates")
            ->where("uuid", $uuid)
            ->firstOrFail();
    }

    public function create(array $data): TaxClass
    {
        return TaxClass::create($data);
    }

    public function update(TaxClass $taxClass, array $data): TaxClass
    {
        $taxClass->update($data);

        return $taxClass->refresh();
    }

    public function changeStatus(TaxClass $taxClass, bool $status): TaxClass
    {
        $taxClass->update([
            "is_active" => $status,
        ]);

        return $taxClass->refresh();
    }

    public function delete(TaxClass $taxClass): bool
    {
        return (bool) $taxClass->delete();
    }

    public function restore(string $uuid): TaxClass
    {
        $taxClass = TaxClass::withTrashed()
            ->where("uuid", $uuid)
            ->firstOrFail();

        $taxClass->restore();

        return $taxClass->refresh();
    }
}
