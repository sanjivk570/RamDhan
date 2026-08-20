<?php

declare(strict_types=1);

namespace App\Modules\Tax\Repositories;

use App\Modules\Tax\Models\TaxRate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaxRateRepository
{
    /**
     * Retrieve paginated tax rates.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return TaxRate::query()

            ->with("taxClass:id,uuid,name,code")

            ->when($filters["search"] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where("name", "LIKE", "%{$search}%")
                        ->orWhere("country_code", "LIKE", "%{$search}%")
                        ->orWhere("state_code", "LIKE", "%{$search}%");
                });
            })

            ->when(!empty($filters["filters"]["tax_class_uuid"]), function (
                $query
            ) use ($filters) {
                $query->whereHas("taxClass", function ($q) use ($filters) {
                    $q->where("uuid", $filters["filters"]["tax_class_uuid"]);
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

            ->when(!empty($filters["filters"]["country_code"]), function (
                $query
            ) use ($filters) {
                $query->where(
                    "country_code",
                    strtoupper($filters["filters"]["country_code"])
                );
            })

            ->when(!empty($filters["filters"]["state_code"]), function (
                $query
            ) use ($filters) {
                $query->where("state_code", $filters["filters"]["state_code"]);
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
                $filters["sort_by"] ?? "priority",
                $filters["sort_order"] ?? "asc"
            )

            ->paginate($filters["per_page"] ?? 20);
    }

    public function findByUuidOrFail(string $uuid): TaxRate
    {
        return TaxRate::with("taxClass")
            ->where("uuid", $uuid)
            ->firstOrFail();
    }

    public function create(array $data): TaxRate
    {
        return TaxRate::create($data);
    }

    public function update(TaxRate $taxRate, array $data): TaxRate
    {
        $taxRate->update($data);

        return $taxRate->refresh();
    }

    public function changeStatus(TaxRate $taxRate, bool $status): TaxRate
    {
        $taxRate->update([
            "is_active" => $status,
        ]);

        return $taxRate->refresh();
    }

    public function delete(TaxRate $taxRate): bool
    {
        return (bool) $taxRate->delete();
    }

    public function restore(string $uuid): TaxRate
    {
        $taxRate = TaxRate::withTrashed()
            ->where("uuid", $uuid)
            ->firstOrFail();

        $taxRate->restore();

        return $taxRate->refresh();
    }
}
