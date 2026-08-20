<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Actions;

use App\Modules\Attribute\Services\AttributeValueService;

class ShowAttributeValueAction
{
    public function __construct(
        private readonly AttributeValueService $attributeValueService
    ) {
    }

    public function execute(string $attributeUuid, string $valueUuid)
    {
        return $this->attributeValueService->details(
            $attributeUuid,
            $valueUuid
        );
    }
}
