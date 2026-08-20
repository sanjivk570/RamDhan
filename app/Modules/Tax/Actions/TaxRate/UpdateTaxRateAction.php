<?php

declare(strict_types=1);

namespace App\Modules\Tax\Actions\TaxRate;

use App\Modules\Tax\Models\TaxRate;
use App\Modules\Tax\Services\TaxRateService;

class UpdateTaxRateAction
{
    public function __construct(private readonly TaxRateService $taxRateService)
    {
    }

    public function execute(string $uuid, array $data): TaxRate
    {
        return $this->taxRateService->update($uuid, $data);
    }
}
