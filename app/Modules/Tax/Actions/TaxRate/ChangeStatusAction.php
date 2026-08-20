<?php

declare(strict_types=1);

namespace App\Modules\Tax\Actions\TaxRate;

use App\Modules\Tax\Models\TaxRate;
use App\Modules\Tax\Services\TaxRateService;

class ChangeStatusAction
{
    public function __construct(private readonly TaxRateService $taxRateService)
    {
    }

    public function execute(string $uuid, bool $status): TaxRate
    {
        return $this->taxRateService->changeStatus($uuid, $status);
    }
}
