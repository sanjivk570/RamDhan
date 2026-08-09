<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Services;

use App\Modules\Attribute\Models\AttributeValue;
use App\Modules\Attribute\Repositories\AttributeRepository;
use App\Modules\Attribute\Repositories\AttributeValueRepository;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AttributeValueService
{
    public function __construct(
        private readonly AttributeRepository $attributeRepository,
        private readonly AttributeValueRepository $attributeValueRepository
    ) {
    }

    /**
     * Create attribute value.
     */
    public function create(string $attributeUuid, array $data): AttributeValue
    {
        $attribute = $this->attributeRepository->findByUuid($attributeUuid);

        if (!$attribute) {
            throw new RuntimeException("Attribute not found.");
        }

        $data["attribute_id"] = $attribute->id;

        return DB::transaction(function () use ($data) {
            return $this->attributeValueRepository->create($data);
        });
    }

    /**
     * Get attribute value.
     */
    public function details(
        string $attributeUuid,
        string $valueUuid
    ): AttributeValue {
        $attribute = $this->attributeRepository->findByUuid($attributeUuid);

        if (!$attribute) {
            throw new RuntimeException("Attribute not found.");
        }

        $value = $this->attributeValueRepository->findByUuidAndAttribute(
            $valueUuid,
            $attribute->id
        );

        if (!$value) {
            throw new RuntimeException("Attribute value not found.");
        }

        return $value->load("attribute");
    }

    /**
     * Update attribute value.
     */
    public function update(
        string $attributeUuid,
        string $valueUuid,
        array $data
    ): AttributeValue {
        $attribute = $this->attributeRepository->findByUuid($attributeUuid);

        if (!$attribute) {
            throw new RuntimeException("Attribute not found.");
        }

        $value = $this->attributeValueRepository->findByUuidAndAttribute(
            $valueUuid,
            $attribute->id
        );

        if (!$value) {
            throw new RuntimeException("Attribute value not found.");
        }

        return DB::transaction(function () use ($value, $data) {
            return $this->attributeValueRepository->update($value, $data);
        });
    }

    /**
     * Delete attribute value.
     */
    public function delete(string $attributeUuid, string $valueUuid): void
    {
        $attribute = $this->attributeRepository->findByUuid($attributeUuid);

        if (!$attribute) {
            throw new RuntimeException("Attribute not found.");
        }

        $value = $this->attributeValueRepository->findByUuidAndAttribute(
            $valueUuid,
            $attribute->id
        );

        if (!$value) {
            throw new RuntimeException("Attribute value not found.");
        }

        DB::transaction(function () use ($value) {
            $this->attributeValueRepository->delete($value);
        });
    }

    /**
     * Restore attribute value.
     */
    public function restore(
        string $attributeUuid,
        string $valueUuid
    ): AttributeValue {
        $attribute = $this->attributeRepository->findByUuid($attributeUuid);

        if (!$attribute) {
            throw new RuntimeException("Attribute not found.");
        }

        $value = AttributeValue::withTrashed()
            ->where("uuid", $valueUuid)
            ->where("attribute_id", $attribute->id)
            ->firstOrFail();

        return DB::transaction(function () use ($value) {
            $value->restore();

            return $value->refresh()->load("attribute");
        });
    }
}
