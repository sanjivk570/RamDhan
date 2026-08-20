<?php

declare(strict_types=1);

namespace App\Modules\Tax\Actions\TaxRate;

use App\Modules\Tax\Services\TaxRateService;

class DeleteTaxRateAction
{
    public function __construct(private readonly TaxRateService $taxRateService)
    {
    }

    public function execute(string $uuid): void
    {
        $this->taxRateService->delete($uuid);
    }
}
