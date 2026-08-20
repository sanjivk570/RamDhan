<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Actions;

use App\Modules\Attribute\Models\Attribute;
use App\Modules\Attribute\Services\AttributeService;

class UpdateAttributeAction
{
    public function __construct(
        private readonly AttributeService $attributeService
    ) {
    }

    public function execute(string $uuid, array $data)
    {
        return $this->attributeService->update($uuid, $data);
    }
}
