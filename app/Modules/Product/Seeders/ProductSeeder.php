<?php

namespace App\Modules\Product\Seeders;

use App\Modules\Category\Models\Category;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed demo products into the database.
 *
 * Creates sample products and assigns them
 * to the first available active category.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class ProductSeeder extends Seeder
{

    /**
     * Run the product database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $categories = Category::query()->where('is_active', true)->get();

        if ($categories->isEmpty()) {
            return;
        }

        $products = [
            [
                'name' => 'Demo Smartphone',
                'slug' => 'demo-smartphone',
                'sku' => 'DEMO-PHONE-001',
                'short_description' =>
                    'Demo smartphone product.',
                'description' =>
                    'Demo product for development.',
                'price' => 29999,
                'compare_price' => 32999,
                'stock_quantity' => 50,
                'low_stock_threshold' => 5,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],

            [
                'name' => 'Demo Laptop',
                'slug' => 'demo-laptop',
                'sku' => 'DEMO-LAPTOP-001',
                'short_description' =>
                    'Demo laptop product.',
                'description' =>
                    'Demo laptop for development.',
                'price' => 59999,
                'compare_price' => 64999,
                'stock_quantity' => 25,
                'low_stock_threshold' => 5,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 2,
            ],
        ];

        foreach ($products as $data) {

            $product =
                Product::firstOrCreate(
                    [
                        'sku' =>
                            $data['sku'],
                    ],
                    [
                        'uuid' =>
                            (string) Str::uuid(),

                        ...$data,
                    ]
                );

            /*
             * Assign first available category.
             */
            $product->categories()->sync([
                $categories->first()->id,
            ]);
        }
    }
}