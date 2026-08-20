<?php

declare(strict_types=1);

namespace App\Modules\Tax\Actions\TaxClass;

use App\Modules\Tax\Services\TaxClassService;

class ListTaxClassAction
{
    public function __construct(
        private readonly TaxClassService $taxClassService
    ) {
    }

    public function execute(array $filters)
    {
        return $this->taxClassService->list($filters);
    }
}
