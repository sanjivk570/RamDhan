<?php

declare(strict_types=1);

namespace App\Modules\Unit\Actions;

use App\Modules\Unit\Models\Unit;
use App\Modules\Unit\Services\UnitService;
use Illuminate\Support\Str;

class CreateUnitAction
{
    public function __construct(private readonly UnitService $unitService)
    {
    }

    public function execute(array $data): Unit
    {
        $data["uuid"] ??= (string) Str::uuid();

        return $this->unitService->create($data);
    }
}
