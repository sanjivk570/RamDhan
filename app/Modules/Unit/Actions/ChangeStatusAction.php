<?php

declare(strict_types=1);

namespace App\Modules\Unit\Actions;

use App\Modules\Unit\Models\Unit;
use App\Modules\Unit\Services\UnitService;

class ChangeStatusAction
{
    public function __construct(private readonly UnitService $unitService)
    {
    }

    public function execute(string $uuid, bool $status): Unit
    {
        return $this->unitService->changeStatus($uuid, $status);
    }
}
