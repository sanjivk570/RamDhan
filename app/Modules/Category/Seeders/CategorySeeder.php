<?php

declare(strict_types=1);

namespace App\Modules\Category\Seeders;

use App\Modules\Category\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::updateOrCreate(
            [
                'slug' => 'electronics',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'parent_id' => null,
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic products and accessories.',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Category::updateOrCreate(
            [
                'slug' => 'mobile',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'parent_id' => $electronics->id,
                'name' => 'Mobile',
                'slug' => 'mobile',
                'description' => 'Mobile phones and smartphones.',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Category::updateOrCreate(
            [
                'slug' => 'laptop',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'parent_id' => $electronics->id,
                'name' => 'Laptop',
                'slug' => 'laptop',
                'description' => 'Laptop and notebook computers.',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        Category::updateOrCreate(
            [
                'slug' => 'accessories',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'parent_id' => $electronics->id,
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Electronic accessories.',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );
    }
}