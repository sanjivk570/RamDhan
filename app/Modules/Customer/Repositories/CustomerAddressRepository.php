<?php

declare(strict_types=1);

namespace App\Modules\Customer\Repositories;

use App\Modules\Customer\Models\CustomerAddress;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerAddressRepository
{
    /**
     * Get customer addresses.
     */
    public function paginate(
        int $customerId,
        array $filters
    ): LengthAwarePaginator {
        return CustomerAddress::query()
            ->where("customer_id", $customerId)

            ->when(
                isset($filters["type"]) && $filters["type"] !== "",
                function ($query) use ($filters) {
                    $query->where("type", $filters["type"]);
                }
            )

            ->when(isset($filters["is_active"]), function ($query) use (
                $filters
            ) {
                $query->where("is_active", (bool) $filters["is_active"]);
            })

            ->orderByDesc("is_default")
            ->orderByDesc("created_at")

            ->paginate($filters["per_page"] ?? 20);
    }

    /**
     * Find address by UUID.
     */
    public function findByUuid(int $customerId, string $uuid): CustomerAddress
    {
        return CustomerAddress::query()
            ->where("customer_id", $customerId)
            ->where("uuid", $uuid)
            ->firstOrFail();
    }

    /**
     * Create address.
     */
    public function create(array $data): CustomerAddress
    {
        return CustomerAddress::create($data);
    }

    /**
     * Update address.
     */
    public function update(
        CustomerAddress $address,
        array $data
    ): CustomerAddress {
        $address->update($data);

        return $address->refresh();
    }

    /**
     * Delete address.
     */
    public function delete(CustomerAddress $address): bool
    {
        return (bool) $address->delete();
    }

    /**
     * Remove default flag from customer's addresses.
     */
    public function clearDefault(
        int $customerId,
        ?string $exceptUuid = null
    ): void {
        CustomerAddress::query()
            ->where("customer_id", $customerId)

            ->when($exceptUuid, function ($query) use ($exceptUuid) {
                $query->where("uuid", "!=", $exceptUuid);
            })

            ->update([
                "is_default" => false,
            ]);
    }
}
