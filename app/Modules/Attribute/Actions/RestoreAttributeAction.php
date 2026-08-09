<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Actions;

use App\Modules\Attribute\Services\AttributeService;

class RestoreAttributeAction
{
    public function __construct(
        private readonly AttributeService $attributeService
    ) {
    }

    public function execute(string $uuid)
    {
        return $this->attributeService->restore($uuid);
    }
}
