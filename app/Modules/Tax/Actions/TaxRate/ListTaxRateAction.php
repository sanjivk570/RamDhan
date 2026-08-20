<?php

declare(strict_types=1);

namespace App\Modules\Tax\Actions\TaxRate;

use App\Modules\Tax\Services\TaxRateService;

class ListTaxRateAction
{
    public function __construct(private readonly TaxRateService $taxRateService)
    {
    }

    public function execute(array $filters)
    {
        return $this->taxRateService->list($filters);
    }
}
