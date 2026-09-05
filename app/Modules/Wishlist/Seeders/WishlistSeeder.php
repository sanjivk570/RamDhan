<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Seeders;

use App\Modules\Customer\Models\Customer;
use App\Modules\Product\Models\Product;
use App\Modules\Wishlist\Models\Wishlist;
use Illuminate\Database\Seeder;

/**
 * Seed demo wishlists.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::query()->take(5)->get();
        $products = Product::query()->where('is_active', true)->take(10)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach ($customers as $index => $customer) {
            foreach ($products->forPage($index + 1, 3) ?: $products->take(2) as $product) {
                Wishlist::firstOrCreate([
                    'customer_id' => $customer->id,
                    'product_id' => $product->id,
                ]);
            }
        }
    }
}
