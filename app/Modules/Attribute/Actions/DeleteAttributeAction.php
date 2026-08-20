<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Actions;

use App\Modules\Attribute\Services\AttributeService;

class DeleteAttributeAction
{
    public function __construct(
        private readonly AttributeService $attributeService
    ) {
    }

    public function execute(string $uuid): void
    {
        $this->attributeService->delete($uuid);
    }
}
