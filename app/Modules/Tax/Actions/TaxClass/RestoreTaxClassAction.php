<?php

declare(strict_types=1);

namespace App\Modules\Tax\Actions\TaxClass;

use App\Modules\Tax\Models\TaxClass;
use App\Modules\Tax\Services\TaxClassService;

class RestoreTaxClassAction
{
    public function __construct(
        private readonly TaxClassService $taxClassService
    ) {
    }

    public function execute(string $uuid): TaxClass
    {
        return $this->taxClassService->restore($uuid);
    }
}
