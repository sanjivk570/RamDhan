<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Actions;
use App\Modules\Purchase\Services\GoodsReceiptService;
final class CreateGoodsReceiptAction
{
    public function __construct(private readonly GoodsReceiptService $service)
    {
    }
    public function execute(array $data, ?int $userId)
    {
        return $this->service->create($data, $userId);
    }
}
