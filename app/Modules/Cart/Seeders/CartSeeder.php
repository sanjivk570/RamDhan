<?php

declare(strict_types=1);

namespace App\Modules\Cart\Seeders;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Customer\Models\Customer;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Seed demo active carts with items.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class CartSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::query()->take(3)->get();
        $products = Product::query()->where('is_active', true)->take(5)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach ($customers as $index => $customer) {
            $cart = Cart::firstOrCreate(
                ['customer_id' => $customer->id, 'status' => Cart::ACTIVE],
                ['currency_code' => 'INR']
            );

            if ($cart->items()->exists()) {
                continue;
            }

            $subtotal = 0;

            foreach ($products->forPage($index + 1, 2) ?: $products->take(2) as $product) {
                $qty = 1 + ($index % 2);
                $lineSubtotal = (float) $product->price * $qty;
                $taxAmount = round($lineSubtotal * 0.18, 2);
                $lineTotal = $lineSubtotal + $taxAmount;
                $subtotal += $lineSubtotal;

                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'sku' => $product->sku,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'compare_price' => $product->compare_price,
                    'tax_rate' => 18,
                    'tax_amount' => $taxAmount,
                    'line_subtotal' => $lineSubtotal,
                    'line_total' => $lineTotal,
                ]);
            }

            $cart->update([
                'subtotal' => $subtotal,
                'tax_amount' => round($subtotal * 0.18, 2),
                'grand_total' => round($subtotal * 1.18, 2),
            ]);
        }
    }
}
