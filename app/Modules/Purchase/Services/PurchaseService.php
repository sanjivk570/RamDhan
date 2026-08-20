<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Services;

use App\Modules\Purchase\Repositories\PurchaseOrderRepository;
use App\Modules\Purchase\Repositories\GoodsReceiptRepository;

final class PurchaseService
{
    public function __construct(
        private readonly PurchaseOrderService $purchaseOrders,
        private readonly GoodsReceiptService $goodsReceipts,
    ) {}

    public function purchaseOrders(): PurchaseOrderService { return $this->purchaseOrders; }
    public function goodsReceipts(): GoodsReceiptService { return $this->goodsReceipts; }
}
