<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Seeders;

use App\Modules\Order\Models\Order;
use App\Modules\SalesReturn\Models\SalesReturn;
use App\Modules\SalesReturn\Models\SalesReturnItem;
use Illuminate\Database\Seeder;

/**
 * Seed a demo sales return for the first delivered order.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class SalesReturnSeeder extends Seeder
{
    public function run(): void
    {
        $order = Order::query()
            ->where('status', Order::DELIVERED)
            ->with('items')
            ->first();

        if (! $order || SalesReturn::where('order_id', $order->id)->exists()) {
            return;
        }

        $item = $order->items->first();

        if (! $item) {
            return;
        }

        $return = SalesReturn::create([
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'status' => 'approved',
            'refund_status' => 'pending',
            'total_amount' => $item->line_total,
            'reason' => 'damaged',
            'customer_note' => 'Package arrived with damaged packaging, item not working.',
            'admin_note' => 'Demo return approved after inspection.',
            'approved_at' => now()->subDay(),
        ]);

        SalesReturnItem::create([
            'sales_return_id' => $return->id,
            'order_item_id' => $item->id,
            'product_id' => $item->product_id,
            'product_variant_id' => $item->product_variant_id,
            'quantity' => 1,
            'unit_price' => $item->unit_price,
            'line_total' => round((float) $item->unit_price * 1.18, 2),
            'reason' => 'Item arrived damaged.',
        ]);
    }
}
