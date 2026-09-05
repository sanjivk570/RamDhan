<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Seeders;

use App\Modules\Inventory\Models\InventoryStock;
use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        Product::query()->chunkById(100, function ($products): void {
            foreach ($products as $product) {
                InventoryStock::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                        'quantity' => (float) ($product->stock_quantity ?? 100),
                        'reserved_quantity' => 0,
                        'low_stock_threshold' => 10,
                        'is_active' => true,
                    ]
                );
            }
        });

        ProductVariant::query()->chunkById(100, function ($variants): void {
            foreach ($variants as $variant) {
                InventoryStock::firstOrCreate(
                    [
                        'product_id' => $variant->product_id,
                        'product_variant_id' => $variant->id,
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                        'quantity' => 50,
                        'reserved_quantity' => 0,
                        'low_stock_threshold' => 5,
                        'is_active' => true,
                    ]
                );
            }
        });
    }
}