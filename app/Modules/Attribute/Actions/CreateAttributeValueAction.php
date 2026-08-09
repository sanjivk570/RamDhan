<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Actions;

use App\Modules\Attribute\Services\AttributeValueService;

class CreateAttributeValueAction
{
    public function __construct(
        private readonly AttributeValueService $attributeValueService
    ) {
    }

    public function execute(string $attributeUuid, array $data)
    {
        return $this->attributeValueService->create($attributeUuid, $data);
    }
}
