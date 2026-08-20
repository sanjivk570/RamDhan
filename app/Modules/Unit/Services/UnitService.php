<?php

declare(strict_types=1);

namespace App\Modules\Unit\Services;

use App\Modules\Unit\Models\Unit;
use App\Modules\Unit\Repositories\UnitRepository;

class UnitService
{
    public function __construct(private readonly UnitRepository $unitRepository)
    {
    }

    /**
     * Retrieve paginated units.
     *
     * @param array<string, mixed> $filters
     */
    public function list(array $filters)
    {
        return $this->unitRepository->paginate($filters);
    }

    /**
     * Retrieve unit details.
     */
    public function details(string $uuid): Unit
    {
        return $this->unitRepository->findByUuidOrFail($uuid);
    }

    /**
     * Create a unit.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Unit
    {
        return $this->unitRepository->create($data);
    }

    /**
     * Update a unit.
     *
     * @param array<string, mixed> $data
     */
    public function update(string $uuid, array $data): Unit
    {
        $unit = $this->unitRepository->findByUuidOrFail($uuid);

        return $this->unitRepository->update($unit, $data);
    }

    /**
     * Change unit status.
     */
    public function changeStatus(string $uuid, bool $status): Unit
    {
        $unit = $this->unitRepository->findByUuidOrFail($uuid);

        return $this->unitRepository->changeStatus($unit, $status);
    }

    /**
     * Delete a unit.
     */
    public function delete(string $uuid): void
    {
        $unit = $this->unitRepository->findByUuidOrFail($uuid);

        $this->unitRepository->delete($unit);
    }

    /**
     * Restore a deleted unit.
     */
    public function restore(string $uuid): Unit
    {
        return $this->unitRepository->restore($uuid);
    }
}
