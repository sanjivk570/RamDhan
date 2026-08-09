<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Services;

use App\Modules\Attribute\Models\Attribute;
use App\Modules\Attribute\Repositories\AttributeRepository;
use Illuminate\Support\Facades\DB;

class AttributeService
{
    public function __construct(
        private readonly AttributeRepository $attributeRepository
    ) {
    }

    /**
     * List attributes.
     */
    public function list(array $filters)
    {
        return $this->attributeRepository->paginate($filters);
    }

    /**
     * Attribute details.
     */
    public function details(string $uuid): Attribute
    {
        return $this->attributeRepository->findByUuidOrFail($uuid);
    }

    /**
     * Create attribute.
     */
    public function create(array $data): Attribute
    {
        return DB::transaction(function () use ($data) {
            return $this->attributeRepository->create($data);
        });
    }

    /**
     * Update attribute.
     */
    public function update(string $uuid, array $data): Attribute
    {
        $attribute = $this->attributeRepository->findByUuidOrFail($uuid);

        return DB::transaction(function () use ($attribute, $data) {
            return $this->attributeRepository->update($attribute, $data);
        });
    }

    /**
     * Delete attribute.
     */
    public function delete(string $uuid): void
    {
        $attribute = $this->attributeRepository->findByUuidOrFail($uuid);

        DB::transaction(function () use ($attribute) {
            $this->attributeRepository->delete($attribute);
        });
    }

    /**
     * Restore attribute.
     */
    public function restore(string $uuid): Attribute
    {
        return DB::transaction(function () use ($uuid) {
            return $this->attributeRepository->restore($uuid);
        });
    }
}
