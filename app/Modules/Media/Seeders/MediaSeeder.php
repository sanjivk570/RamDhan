<?php

declare(strict_types=1);

namespace App\Modules\Media\Seeders;

use App\Modules\Media\Models\Media;
use App\Modules\Product\Models\Product;
use App\Modules\Slider\Models\SliderItem;
use Illuminate\Database\Seeder;

/**
 * Seed demo media records for products and slider items.
 *
 * Creates polymorphic media rows (collection: product / slider)
 * pointing at placeholder images served from the public disk.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class MediaSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Product gallery images.
         */
        Product::query()->chunkById(100, function ($products): void {
            foreach ($products as $index => $product) {
                $exists = Media::where('mediable_type', Product::class)
                    ->where('mediable_id', $product->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                for ($i = 1; $i <= 2; $i++) {
                    Media::create([
                        'mediable_type' => Product::class,
                        'mediable_id' => $product->id,
                        'original_name' => "product-{$product->id}-{$i}.jpg",
                        'file_name' => "products/product-{$product->id}-{$i}.jpg",
                        'collection' => 'product',
                        'disk' => 'public',
                        'path' => "storage/products/product-{$product->id}-{$i}.jpg",
                        'mime_type' => 'image/jpeg',
                        'size' => 120000 + ($i * 15000),
                        'title' => $product->name . ' — image ' . $i,
                        'alt_text' => $product->name,
                        'type' => 'image',
                        'sort_order' => $i,
                        'is_primary' => $i === 1,
                    ]);
                }
            }
        });

        /*
         * Slider slide banners.
         */
        SliderItem::query()->chunkById(100, function ($items): void {
            foreach ($items as $index => $item) {
                $exists = Media::where('mediable_type', SliderItem::class)
                    ->where('mediable_id', $item->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                Media::create([
                    'mediable_type' => SliderItem::class,
                    'mediable_id' => $item->id,
                    'original_name' => "slide-{$item->id}.jpg",
                    'file_name' => "slides/slide-{$item->id}.jpg",
                    'collection' => 'slider',
                    'disk' => 'public',
                    'path' => "storage/slides/slide-{$item->id}.jpg",
                    'mime_type' => 'image/jpeg',
                    'size' => 250000 + ($index * 12000),
                    'title' => $item->title,
                    'alt_text' => $item->title,
                    'type' => 'image',
                    'sort_order' => 1,
                    'is_primary' => true,
                ]);
            }
        });
    }
}
