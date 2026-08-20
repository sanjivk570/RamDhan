<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Repositories;

use App\Modules\Shipping\Models\ShippingMethod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShippingMethodRepository
{
    public function paginate(
        array $filters
    ): LengthAwarePaginator {

        return ShippingMethod::query()

            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'name',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'code',
                            'LIKE',
                            "%{$search}%"
                        );
                    });
                }
            )

            ->when(
                isset($filters['status']) &&
                $filters['status'] !== '',
                function ($query) use ($filters) {

                    $query->where(
                        'is_active',
                        (bool) $filters['status']
                    );
                }
            )

            ->orderBy(
                $filters['sort_by'] ?? 'sort_order',
                $filters['sort_order'] ?? 'asc'
            )

            ->paginate(
                $filters['per_page'] ?? 20
            );
    }

    public function findByUuidOrFail(
        string $uuid
    ): ShippingMethod {

        return ShippingMethod::query()
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function create(array $data): ShippingMethod
    {
        return ShippingMethod::create($data);
    }

    public function update(
        ShippingMethod $method,
        array $data
    ): ShippingMethod {

        $method->update($data);

        return $method->refresh();
    }

    public function delete(
        ShippingMethod $method
    ): bool {

        return (bool) $method->delete();
    }
}