<?php

declare(strict_types=1);

namespace App\Modules\Tax\Services;

use App\Modules\Tax\Models\TaxRate;
use App\Modules\Tax\Repositories\TaxClassRepository;
use App\Modules\Tax\Repositories\TaxRateRepository;

class TaxRateService
{
    public function __construct(
        private readonly TaxRateRepository $taxRateRepository,
        private readonly TaxClassRepository $taxClassRepository
    ) {
    }

    public function list(array $filters)
    {
        return $this->taxRateRepository->paginate($filters);
    }

    public function details(string $uuid): TaxRate
    {
        return $this->taxRateRepository->findByUuidOrFail($uuid);
    }

    public function create(array $data): TaxRate
    {
        /*
         * API accepts tax class UUID.
         * Database stores tax_class_id.
         */
        $taxClass = $this->taxClassRepository->findByUuidOrFail(
            $data["tax_class_uuid"]
        );

        unset($data["tax_class_uuid"]);

        $data["tax_class_id"] = $taxClass->id;

        return $this->taxRateRepository->create($data);
    }

    public function update(string $uuid, array $data): TaxRate
    {
        $taxRate = $this->taxRateRepository->findByUuidOrFail($uuid);

        $taxClass = $this->taxClassRepository->findByUuidOrFail(
            $data["tax_class_uuid"]
        );

        unset($data["tax_class_uuid"]);

        $data["tax_class_id"] = $taxClass->id;

        return $this->taxRateRepository->update($taxRate, $data);
    }

    public function changeStatus(string $uuid, bool $status): TaxRate
    {
        $taxRate = $this->taxRateRepository->findByUuidOrFail($uuid);

        return $this->taxRateRepository->changeStatus($taxRate, $status);
    }

    public function delete(string $uuid): void
    {
        $taxRate = $this->taxRateRepository->findByUuidOrFail($uuid);

        $this->taxRateRepository->delete($taxRate);
    }

    public function restore(string $uuid): TaxRate
    {
        return $this->taxRateRepository->restore($uuid);
    }
}
