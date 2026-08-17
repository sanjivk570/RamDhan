<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Actions;
use App\Modules\Purchase\Services\GoodsReceiptService;
final class ShowGoodsReceiptAction
{
    public function __construct(private readonly GoodsReceiptService $service)
    {
    }
    public function execute(string $uuid)
    {
        return $this->service->details($uuid);
    }
}
