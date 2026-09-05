<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Seeders;

use App\Modules\Attribute\Models\Attribute;
use App\Modules\Attribute\Models\AttributeValue;
use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariant;
use App\Modules\ProductVariant\Models\ProductVariantAttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed demo product variants.
 *
 * Creates a "Demo T-Shirt" product with Size/Color variants
 * and links attribute values to each variant.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::firstOrCreate(
            ['slug' => 'demo-t-shirt'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Demo T-Shirt',
                'sku' => 'DEMO-TSHIRT-001',
                'short_description' => 'Demo variable product.',
                'description' => 'Demo t-shirt with size and color variants.',
                'price' => 499,
                'compare_price' => 699,
                'cost_price' => 250,
                //'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        $sizeAttribute = Attribute::where('slug', 'size')->first();
        $colorAttribute = Attribute::where('slug', 'color')->first();

        if (! $sizeAttribute || ! $colorAttribute) {
            return;
        }

        $sizes = AttributeValue::where('attribute_id', $sizeAttribute->id)->get();
        $colors = AttributeValue::where('attribute_id', $colorAttribute->id)->take(2)->get();

        foreach ($sizes as $size) {
            foreach ($colors as $color) {
                $sku = 'DEMO-TS-' . strtoupper($size->slug . '-' . $color->slug);

                $variant = ProductVariant::firstOrCreate(
                    ['sku' => $sku],
                    [
                        'uuid' => (string) Str::uuid(),
                        'product_id' => $product->id,
                        'name' => $product->name . ' - ' . $size->display_value . ' / ' . $color->display_value,
                        'price' => 499 + ($size->slug === 'xl' ? 50 : 0),
                        'compare_price' => 699,
                        'cost_price' => 250,
                        'is_default' => $size->sort_order === 1 && $color->sort_order === 1,
                        'is_active' => true,
                    ]
                );

                foreach ([$size->id, $color->id] as $valueId) {
                    ProductVariantAttributeValue::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'attribute_value_id' => $valueId,
                        ]
                    );
                }
            }
        }
    }
}
