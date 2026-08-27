<?php

declare(strict_types=1);

namespace App\Modules\Slider\Seeders;

use App\Modules\Slider\Models\Slider;
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
    }
}
