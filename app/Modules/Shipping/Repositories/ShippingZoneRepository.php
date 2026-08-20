<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Repositories;

use App\Modules\Shipping\Models\ShippingZone;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShippingZoneRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return ShippingZone::query()

            ->when($filters["search"] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where("name", "LIKE", "%{$search}%")->orWhere(
                        "code",
                        "LIKE",
                        "%{$search}%"
                    );
                });
            })

            ->when(
                isset($filters["status"]) && $filters["status"] !== "",
                function ($query) use ($filters) {
                    $query->where("is_active", (bool) $filters["status"]);
                }
            )

            ->orderBy(
                $filters["sort_by"] ?? "sort_order",
                $filters["sort_order"] ?? "asc"
            )

            ->paginate($filters["per_page"] ?? 20);
    }

    public function findByUuidOrFail(string $uuid): ShippingZone
    {
        return ShippingZone::query()
            ->where("uuid", $uuid)
            ->firstOrFail();
    }

    public function create(array $data): ShippingZone
    {
        return ShippingZone::create($data);
    }

    public function update(ShippingZone $zone, array $data): ShippingZone
    {
        $zone->update($data);

        return $zone->refresh();
    }

    public function delete(ShippingZone $zone): bool
    {
        return (bool) $zone->delete();
    }
}
