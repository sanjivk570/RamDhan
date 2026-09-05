<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Seeders;

use App\Modules\Order\Models\Order;
use App\Modules\SalesInvoice\Models\SalesInvoice;
use App\Modules\SalesInvoice\Models\SalesInvoiceItem;
use Illuminate\Database\Seeder;

/**
 * Seed demo sales invoices for paid/delivered orders.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class SalesInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        Order::query()
            ->whereIn('status', [Order::SHIPPED, Order::DELIVERED])
            ->with('items')
            ->chunkById(100, function ($orders): void {
                foreach ($orders as $order) {
                    if (SalesInvoice::where('order_id', $order->id)->exists()) {
                        continue;
                    }

                    $invoice = SalesInvoice::create([
                        'order_id' => $order->id,
                        'customer_id' => $order->customer_id,
                        'status' => 'paid',
                        'invoice_date' => now()->subDays(5)->toDateString(),
                        'due_date' => now()->addDays(10)->toDateString(),
                        'currency_code' => $order->currency_code,
                        'subtotal' => $order->subtotal,
                        'discount_amount' => $order->discount_amount,
                        'tax_amount' => $order->tax_amount,
                        'shipping_amount' => $order->shipping_amount,
                        'grand_total' => $order->grand_total,
                        'paid_amount' => $order->grand_total,
                        'due_amount' => 0,
                        'billing_address' => $order->billing_address,
                    ]);

                    foreach ($order->items as $item) {
                        SalesInvoiceItem::create([
                            'sales_invoice_id' => $invoice->id,
                            'order_item_id' => $item->id,
                            'product_id' => $item->product_id,
                            'product_variant_id' => $item->product_variant_id,
                            'sku' => $item->sku,
                            'description' => $item->product_name,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'discount_amount' => $item->discount_amount,
                            'tax_rate' => $item->tax_rate,
                            'tax_amount' => $item->tax_amount,
                            'line_subtotal' => $item->line_subtotal,
                            'line_total' => $item->line_total,
                        ]);
                    }
                }
            });
    }
}
