<?php

declare(strict_types=1);

namespace App\Modules\Slider\Seeders;

use App\Modules\Slider\Models\Slider;
use App\Modules\Slider\Models\SliderItem;
use Illuminate\Database\Seeder;

/**
 * Seed a default set of sliders for the storefront.
 *
 * Creates the "Home Hero Slider" (code: home_hero) used on
 * the home page, plus a secondary placeholder slider.
 *
 * @package App\Modules\Slider\Seeders
 * @author Sanjiv Kumar Kushwaha
 */
class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $hero = Slider::firstOrCreate(
            ['code' => 'home_hero'],
            [
                'name'      => 'Home Hero Slider',
                'placement' => 'home',
                'is_active' => true,
            ]
        );

        $heroSecond = Slider::firstOrCreate(
            ['code' => 'home_secondary'],
            [
                'name'      => 'Home Secondary Slider',
                'placement' => 'home',
                'is_active' => true,
            ]
        );

        /*
         * Demo slides for the hero slider.
         */
        $slides = [
            [
                'code' => 'home_hero',
                'items' => [
                    [
                        'title' => 'RamDhan Mega Sale',
                        'subtitle' => 'Up to 50% off on electronics, fashion & more',
                        'button_text' => 'Shop Now',
                        'button_url' => '/products',
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'New Arrivals',
                        'subtitle' => 'Fresh styles for the season — shop the latest drops',
                        'button_text' => 'Explore',
                        'button_url' => '/products?sort=newest',
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'Free Shipping',
                        'subtitle' => 'On all orders above ₹999 across India',
                        'button_text' => 'Start Shopping',
                        'button_url' => '/products',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'code' => 'home_secondary',
                'items' => [
                    [
                        'title' => 'Festive Collection 2026',
                        'subtitle' => 'Curated picks for your celebrations',
                        'button_text' => 'View Collection',
                        'button_url' => '/products?tag=festive',
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Best Sellers',
                        'subtitle' => 'Most loved products by our customers',
                        'button_text' => 'See All',
                        'button_url' => '/products?sort=popular',
                        'sort_order' => 2,
                    ],
                ],
            ],
        ];

        foreach ($slides as $group) {
            $slider = $group['code'] === 'home_hero' ? $hero : $heroSecond;

            foreach ($group['items'] as $item) {
                SliderItem::firstOrCreate(
                    [
                        'slider_id' => $slider->id,
                        'title' => $item['title'],
                    ],
                    $item + ['is_active' => true]
                );
            }
        }
    }
}
