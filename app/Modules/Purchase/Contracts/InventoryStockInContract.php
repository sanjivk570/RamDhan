<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Contracts;

use App\Modules\Purchase\Models\GoodsReceipt;

interface InventoryStockInContract
{
    // /** @param array<int, array<string,mixed>> $items */
    // public function post(GoodsReceipt $receipt, array $items): void;

    // /** @param array<int, array<string,mixed>> $items */
    // public function reverse(GoodsReceipt $receipt, array $items): void;

    /**
     * Post accepted GRN quantities into inventory.
     *
     * @param GoodsReceipt $receipt
     * @param array<int, array<string, mixed>> $items
     * @param int|null $createdBy
     */
    public function post(GoodsReceipt $receipt, array $items, ?int $createdBy = null): void;

    /**
     * Reverse previously posted GRN quantities from inventory.
     *
     * @param GoodsReceipt $receipt
     * @param array<int, array<string, mixed>> $items
     * @param int|null $createdBy
     */
    public function reverse(GoodsReceipt $receipt, array $items, ?int $createdBy = null): void;
}
