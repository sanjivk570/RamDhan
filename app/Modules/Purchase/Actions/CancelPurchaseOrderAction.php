<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Actions;
use App\Modules\Purchase\Services\PurchaseOrderService;
final class CancelPurchaseOrderAction
{
    public function __construct(private readonly PurchaseOrderService $service)
    {
    }
    public function execute(string $uuid, ?int $userId, ?string $reason)
    {
        return $this->service->cancel($uuid, $userId, $reason);
    }
}
