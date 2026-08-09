<?php

declare(strict_types=1);

namespace App\Modules\Tax\Services;

use App\Modules\Tax\Models\TaxClass;
use App\Modules\Tax\Repositories\TaxClassRepository;

class TaxClassService
{
    public function __construct(
        private readonly TaxClassRepository $taxClassRepository
    ) {
    }

    public function list(array $filters)
    {
        return $this->taxClassRepository->paginate($filters);
    }

    public function details(string $uuid): TaxClass
    {
        return $this->taxClassRepository->findByUuidOrFail($uuid);
    }

    public function create(array $data): TaxClass
    {
        return $this->taxClassRepository->create($data);
    }

    public function update(string $uuid, array $data): TaxClass
    {
        $taxClass = $this->taxClassRepository->findByUuidOrFail($uuid);

        return $this->taxClassRepository->update($taxClass, $data);
    }

    public function changeStatus(string $uuid, bool $status): TaxClass
    {
        $taxClass = $this->taxClassRepository->findByUuidOrFail($uuid);

        return $this->taxClassRepository->changeStatus($taxClass, $status);
    }

    public function delete(string $uuid): void
    {
        $taxClass = $this->taxClassRepository->findByUuidOrFail($uuid);

        $this->taxClassRepository->delete($taxClass);
    }

    public function restore(string $uuid): TaxClass
    {
        return $this->taxClassRepository->restore($uuid);
    }
}
