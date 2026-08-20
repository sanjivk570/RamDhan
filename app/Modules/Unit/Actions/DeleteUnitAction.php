<?php

declare(strict_types=1);

namespace App\Modules\Unit\Actions;

use App\Modules\Unit\Services\UnitService;

class DeleteUnitAction
{
    public function __construct(private readonly UnitService $unitService)
    {
    }

    public function execute(string $uuid): void
    {
        $this->unitService->delete($uuid);
    }
}
