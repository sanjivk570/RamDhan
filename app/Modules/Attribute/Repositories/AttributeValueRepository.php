<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Repositories;

use App\Modules\Attribute\Models\AttributeValue;

class AttributeValueRepository
{
    /**
     * Find value by UUID.
     */
    public function findByUuid(string $uuid): ?AttributeValue
    {
        return AttributeValue::query()
            ->with("attribute")
            ->where("uuid", $uuid)
            ->first();
    }

    /**
     * Find value by UUID or fail.
     */
    public function findByUuidOrFail(string $uuid): AttributeValue
    {
        return AttributeValue::query()
            ->with("attribute")
            ->where("uuid", $uuid)
            ->firstOrFail();
    }

    /**
     * Find value belonging to attribute.
     */
    public function findByUuidAndAttribute(
        string $uuid,
        int $attributeId
    ): ?AttributeValue {
        return AttributeValue::query()
            ->where("uuid", $uuid)
            ->where("attribute_id", $attributeId)
            ->first();
    }

    /**
     * Create value.
     */
    public function create(array $data): AttributeValue
    {
        return AttributeValue::create($data);
    }

    /**
     * Update value.
     */
    public function update(
        AttributeValue $attributeValue,
        array $data
    ): AttributeValue {
        $attributeValue->update($data);

        return $attributeValue->refresh();
    }

    /**
     * Delete value.
     */
    public function delete(AttributeValue $attributeValue): bool
    {
        return (bool) $attributeValue->delete();
    }

    /**
     * Restore value.
     */
    public function restore(string $uuid): AttributeValue
    {
        $attributeValue = AttributeValue::withTrashed()
            ->where("uuid", $uuid)
            ->firstOrFail();

        $attributeValue->restore();

        return $attributeValue->refresh();
    }
}
