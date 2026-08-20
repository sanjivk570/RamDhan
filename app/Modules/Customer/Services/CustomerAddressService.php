<?php

declare(strict_types=1);

namespace App\Modules\Customer\Services;

use App\Modules\Customer\Models\CustomerAddress;
use App\Modules\Customer\Repositories\CustomerAddressRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerAddressService
{
    public function __construct(
        private readonly CustomerAddressRepository $repository
    ) {
    }

    /**
     * List addresses.
     */
    public function list(int $customerId, array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate($customerId, $filters);
    }

    /**
     * Get address.
     */
    public function details(int $customerId, string $uuid): CustomerAddress
    {
        return $this->repository->findByUuid($customerId, $uuid);
    }

    /**
     * Create address.
     */
    public function create(int $customerId, array $data): CustomerAddress
    {
        return DB::transaction(function () use ($customerId, $data) {
            $data["customer_id"] = $customerId;

            $isDefault = (bool) ($data["is_default"] ?? false);

            /*
             * First address should automatically
             * become default.
             */
            if (
                !$this->repository
                    ->paginate($customerId, ["per_page" => 1])
                    ->total()
            ) {
                $isDefault = true;
            }

            if ($isDefault) {
                $this->repository->clearDefault($customerId);
            }

            $data["is_default"] = $isDefault;

            return $this->repository->create($data);
        });
    }

    /**
     * Update address.
     */
    public function update(
        int $customerId,
        string $uuid,
        array $data
    ): CustomerAddress {
        return DB::transaction(function () use ($customerId, $uuid, $data) {
            $address = $this->repository->findByUuid($customerId, $uuid);

            if (isset($data["is_default"]) && $data["is_default"] === true) {
                $this->repository->clearDefault($customerId, $uuid);
            }

            return $this->repository->update($address, $data);
        });
    }

    /**
     * Delete address.
     */
    public function delete(int $customerId, string $uuid): void
    {
        DB::transaction(function () use ($customerId, $uuid) {
            $address = $this->repository->findByUuid($customerId, $uuid);

            $wasDefault = $address->is_default;

            $this->repository->delete($address);

            /*
             * If default address was deleted,
             * automatically select another address.
             */
            if ($wasDefault) {
                $nextAddress = CustomerAddress::query()
                    ->where("customer_id", $customerId)
                    ->where("is_active", true)
                    ->orderByDesc("created_at")
                    ->first();

                if ($nextAddress) {
                    $nextAddress->update([
                        "is_default" => true,
                    ]);
                }
            }
        });
    }

    /**
     * Set address as default.
     */
    public function setDefault(int $customerId, string $uuid): CustomerAddress
    {
        return DB::transaction(function () use ($customerId, $uuid) {
            $address = $this->repository->findByUuid($customerId, $uuid);

            $this->repository->clearDefault($customerId, $uuid);

            return $this->repository->update($address, [
                "is_default" => true,
            ]);
        });
    }
}
