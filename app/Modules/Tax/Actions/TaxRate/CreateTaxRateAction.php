<?php

declare(strict_types=1);

namespace App\Modules\Tax\Actions\TaxRate;

use App\Modules\Tax\Models\TaxRate;
use App\Modules\Tax\Services\TaxRateService;
use Illuminate\Support\Str;

class CreateTaxRateAction
{
    public function __construct(private readonly TaxRateService $taxRateService)
    {
    }

    public function execute(array $data): TaxRate
    {
        $data["uuid"] ??= (string) Str::uuid();

        return $this->taxRateService->create($data);
    }
}
