<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Actions;
use App\Modules\Purchase\Services\PurchaseOrderService;
final class UpdatePurchaseOrderAction
{
    public function __construct(private readonly PurchaseOrderService $service)
    {
    }
    public function execute(string $uuid, array $data)
    {
        return $this->service->update($uuid, $data);
    }
}
