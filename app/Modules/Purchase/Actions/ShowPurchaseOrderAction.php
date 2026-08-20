<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Actions;
use App\Modules\Purchase\Services\PurchaseOrderService;
final class ShowPurchaseOrderAction
{
    public function __construct(private readonly PurchaseOrderService $service)
    {
    }
    public function execute(string $uuid)
    {
        return $this->service->details($uuid);
    }
}
