<?php

declare(strict_types=1);

namespace App\Modules\Unit\Actions;

use App\Modules\Unit\Services\UnitService;

class ListUnitAction
{
    public function __construct(private readonly UnitService $unitService)
    {
    }

    public function execute(array $filters)
    {
        return $this->unitService->list($filters);
    }
}
