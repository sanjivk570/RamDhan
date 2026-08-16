<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Services;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\Supplier\Repositories\SupplierRepository;

class SupplierService
{
    public function __construct(
        private readonly SupplierRepository $supplierRepository
    ) {
    }

    /**
     * List suppliers.
     */
    public function list(array $filters)
    {
        return $this->supplierRepository->paginate($filters);
    }

    /**
     * Get supplier details.
     */
    public function details(string $uuid): Supplier
    {
        return $this->supplierRepository->findByUuidOrFail($uuid);
    }

    /**
     * Create supplier.
     */
    public function create(array $data): Supplier
    {
        return $this->supplierRepository->create($data);
    }

    /**
     * Update supplier.
     */
    public function update(string $uuid, array $data): Supplier
    {
        $supplier = $this->supplierRepository->findByUuidOrFail($uuid);

        return $this->supplierRepository->update($supplier, $data);
    }

    /**
     * Change supplier status.
     */
    public function changeStatus(string $uuid, bool $status): Supplier
    {
        $supplier = $this->supplierRepository->findByUuidOrFail($uuid);

        return $this->supplierRepository->changeStatus($supplier, $status);
    }

    /**
     * Delete supplier.
     */
    public function delete(string $uuid): void
    {
        $supplier = $this->supplierRepository->findByUuidOrFail($uuid);

        $this->supplierRepository->delete($supplier);
    }

    /**
     * Restore supplier.
     */
    public function restore(string $uuid): Supplier
    {
        return $this->supplierRepository->restore($uuid);
    }
}
