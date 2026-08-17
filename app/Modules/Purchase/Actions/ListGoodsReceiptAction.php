<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Actions;
use App\Modules\Purchase\Services\GoodsReceiptService;
final class ListGoodsReceiptAction
{
    public function __construct(private readonly GoodsReceiptService $service)
    {
    }
    public function execute(array $filters)
    {
        return $this->service->list($filters);
    }
}
