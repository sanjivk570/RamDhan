<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Seeders;

use App\Modules\Attribute\Models\Attribute;
use App\Modules\Attribute\Models\AttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed demo product attributes and values.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            'Color' => ['Red', 'Blue', 'Black', 'White', 'Green'],
            'Size' => ['Small', 'Medium', 'Large', 'XL'],
            'Material' => ['Cotton', 'Leather', 'Plastic', 'Metal'],
        ];

        $sort = 1;

        foreach ($attributes as $name => $values) {
            $attribute = Attribute::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $name,
                    'type' => 'select',
                    'sort_order' => $sort++,
                    'is_active' => true,
                ]
            );

            $valueSort = 1;

            foreach ($values as $value) {
                AttributeValue::withTrashed()->updateOrCreate(
                    [
                        'attribute_id' => $attribute->id,
                        'slug' => Str::slug($value),
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                        'value' => strtolower($value),
                        'display_value' => $value,
                        'sort_order' => $valueSort++,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
