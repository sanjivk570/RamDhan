<?php

declare(strict_types=1);

namespace App\Modules\Order\Seeders;

use App\Modules\Customer\Models\Customer;
use App\Modules\Customer\Models\CustomerAddress;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use App\Modules\Order\Models\OrderStatusHistory;
use App\Modules\Payment\Models\PaymentTransaction;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seed demo orders with items, status histories and payment
 * transactions. Delivered orders additionally get a shipment,
 * sales invoice and a sales return created by their own seeders.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::query()->take(4)->get();
        $products = Product::query()->where('is_active', true)->take(6)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        $statuses = [Order::DELIVERED, Order::SHIPPED, Order::PROCESSING, Order::PENDING];

        foreach ($customers as $index => $customer) {
            if (Order::where('customer_id', $customer->id)->exists()) {
                continue;
            }

            $status = $statuses[$index % count($statuses)];
            $address = CustomerAddress::where('customer_id', $customer->id)->first();

            $addressData = [
                'name' => $customer->full_name,
                'phone' => $customer->mobile ?? '9876500000',
                'address_line_1' => $address->address_line_1 ?? 'House 12, Model Town',
                'city' => $address->city ?? 'Ludhiana',
                'state' => $address->state ?? 'Punjab',
                'postal_code' => $address->postal_code ?? '141002',
                'country' => 'India',
                'country_code' => 'IN',
            ];

            $orderProducts = $products->forPage($index + 1, 2) ?: $products->take(2);

            $order = DB::transaction(function () use ($customer, $status, $addressData, $orderProducts, $address, $statuses) {
                $subtotal = 0;
                $lines = [];

                foreach ($orderProducts as $product) {
                    $qty = 1 + (int) ($product->id % 2);
                    $lineSubtotal = (float) $product->price * $qty;
                    $taxAmount = round($lineSubtotal * 0.18, 2);
                    $subtotal += $lineSubtotal;

                    $lines[] = [
                        'product_id' => $product->id,
                        'sku' => $product->sku,
                        'product_name' => $product->name,
                        'quantity' => $qty,
                        'unit_price' => $product->price,
                        'tax_rate' => 18,
                        'tax_amount' => $taxAmount,
                        'line_subtotal' => $lineSubtotal,
                        'line_total' => $lineSubtotal + $taxAmount,
                    ];
                }

                $shippingAmount = 99;
                $grandTotal = round($subtotal * 1.18 + $shippingAmount, 2);

                $order = Order::create([
                    'customer_id' => $customer->id,
                    'customer_email' => $customer->email,
                    'customer_name' => $customer->full_name,
                    'customer_phone' => $customer->mobile ?? null,
                    'status' => $status,
                    'payment_status' => in_array($status, [Order::DELIVERED, Order::SHIPPED, Order::PROCESSING], true)
                        ? 'paid'
                        : 'pending',
                    'fulfillment_status' => match ($status) {
                        Order::DELIVERED => 'delivered',
                        Order::SHIPPED => 'shipped',
                        default => 'unfulfilled',
                    },
                    'currency_code' => 'INR',
                    'subtotal' => $subtotal,
                    'tax_amount' => round($subtotal * 0.18, 2),
                    'shipping_amount' => $shippingAmount,
                    'grand_total' => $grandTotal,
                    'shipping_method_name' => 'Standard Shipping',
                    'shipping_method_code' => 'STD',
                    'payment_method' => 'cod',
                    'billing_address' => $addressData,
                    'shipping_address' => $addressData,
                    'placed_at' => now()->subDays(count($statuses) - array_search($status, $statuses, true) + 3),
                ]);

                foreach ($lines as $line) {
                    OrderItem::create(['order_id' => $order->id, ...$line]);
                }

                $historyFlow = [Order::PENDING, Order::CONFIRMED, Order::PROCESSING];
                if ($status === Order::PENDING) {
                    $historyFlow = [Order::PENDING];
                } elseif ($status === Order::SHIPPED) {
                    $historyFlow = [Order::PENDING, Order::CONFIRMED, Order::SHIPPED];
                } elseif ($status === Order::DELIVERED) {
                    $historyFlow = [Order::PENDING, Order::CONFIRMED, Order::PROCESSING, Order::SHIPPED, Order::DELIVERED];
                }

                foreach ($historyFlow as $i => $historyStatus) {
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'from_status' => $i === 0 ? null : $historyFlow[$i - 1],
                        'to_status' => $historyStatus,
                        'source' => 'seed',
                        'note' => ucfirst($historyStatus),
                        'changed_by' => null,
                    ]);
                }

                return $order;
            });

            // Payment transaction for paid orders.
            if ($order->payment_status === 'paid') {
                PaymentTransaction::create([
                    'order_id' => $order->id,
                    'provider' => 'cod',
                    'transaction_type' => 'capture',
                    'status' => 'success',
                    'provider_transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                    'amount' => $order->grand_total,
                    'currency_code' => 'INR',
                    'payment_method' => 'cod',
                    'reference_number' => 'REF-' . strtoupper(Str::random(8)),
                ]);
            }
        }
    }
}
