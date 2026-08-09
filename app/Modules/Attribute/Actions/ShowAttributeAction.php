<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Actions;

use App\Modules\Attribute\Models\Attribute;
use App\Modules\Attribute\Services\AttributeService;

class ShowAttributeAction
{
    public function __construct(
        private readonly AttributeService $attributeService
    ) {
    }

    public function execute(string $uuid): Attribute
    {
        return $this->attributeService->details($uuid);
    }
}
