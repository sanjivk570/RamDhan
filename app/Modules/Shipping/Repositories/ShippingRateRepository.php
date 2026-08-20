<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Repositories;

use App\Modules\Shipping\Models\ShippingRate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShippingRateRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return ShippingRate::query()
            ->with(["zone", "method"])

            ->when($filters["zone_uuid"] ?? null, function ($query, $uuid) {
                $query->whereHas("zone", fn($q) => $q->where("uuid", $uuid));
            })

            ->when($filters["method_uuid"] ?? null, function ($query, $uuid) {
                $query->whereHas("method", fn($q) => $q->where("uuid", $uuid));
            })

            ->when(
                isset($filters["status"]) && $filters["status"] !== "",
                function ($query) use ($filters) {
                    $query->where("is_active", (bool) $filters["status"]);
                }
            )

            // ->orderBy(
            //     $filters["sort_by"] ?? "sort_order",
            //     $filters["sort_order"] ?? "asc"
            // )

            ->paginate($filters["per_page"] ?? 20);
    }

    public function findByUuidOrFail(string $uuid): ShippingRate
    {
        return ShippingRate::query()
            ->with(["zone", "method"])
            ->where("uuid", $uuid)
            ->firstOrFail();
    }

    public function create(array $data): ShippingRate
    {
        return ShippingRate::create($data);
    }

    public function update(ShippingRate $rate, array $data): ShippingRate
    {
        $rate->update($data);

        return $rate->refresh();
    }

    public function delete(ShippingRate $rate): bool
    {
        return (bool) $rate->delete();
    }
}
