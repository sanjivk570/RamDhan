<?php

declare(strict_types=1);

namespace App\Modules\Tax\Actions\TaxClass;

use App\Modules\Tax\Models\TaxClass;
use App\Modules\Tax\Services\TaxClassService;
use Illuminate\Support\Str;

class CreateTaxClassAction
{
    public function __construct(
        private readonly TaxClassService $taxClassService
    ) {
    }

    public function execute(array $data): TaxClass
    {
        $data["uuid"] ??= (string) Str::uuid();

        return $this->taxClassService->create($data);
    }
}
