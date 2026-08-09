<?php

declare(strict_types=1);

namespace App\Modules\Tax\Actions\TaxClass;

use App\Modules\Tax\Services\TaxClassService;

class DeleteTaxClassAction
{
    public function __construct(
        private readonly TaxClassService $taxClassService
    ) {
    }

    public function execute(string $uuid): void
    {
        $this->taxClassService->delete($uuid);
    }
}
