<?php

declare(strict_types=1);

namespace App\Modules\Tax\Seeders;

use App\Modules\Tax\Models\TaxClass;
use App\Modules\Tax\Models\TaxRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed demo tax classes and rates.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['name' => 'Standard Rate', 'code' => 'STD', 'description' => 'Standard GST rate', 'sort_order' => 1],
            ['name' => 'Reduced Rate', 'code' => 'RDC', 'description' => 'Reduced tax rate for essentials', 'sort_order' => 2],
            ['name' => 'Zero Rate', 'code' => 'ZRO', 'description' => 'Zero-rated / exempt goods', 'sort_order' => 3],
        ];

        foreach ($classes as $data) {
            $class = TaxClass::firstOrCreate(['code' => $data['code']], ['uuid' => (string) Str::uuid()] + $data + ['is_active' => true]);
        }

        $rates = [
            ['code' => 'STD', 'name' => 'GST 18%', 'rate' => 18.00, 'country_code' => 'IN', 'priority' => 1],
            ['code' => 'STD', 'name' => 'GST 12%', 'rate' => 12.00, 'country_code' => 'IN', 'priority' => 2],
            ['code' => 'RDC', 'name' => 'GST 5%', 'rate' => 5.00, 'country_code' => 'IN', 'priority' => 1],
            ['code' => 'ZRO', 'name' => 'GST 0%', 'rate' => 0.00, 'country_code' => 'IN', 'priority' => 1],
        ];

        foreach ($rates as $rate) {
            $classId = TaxClass::where('code', $rate['code'])->value('id');

            TaxRate::firstOrCreate(
                [
                    'tax_class_id' => $classId,
                    'name' => $rate['name'],
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'rate' => $rate['rate'],
                    'country_code' => $rate['country_code'],
                    'state_code' => null,
                    'is_active' => true,
                    'priority' => $rate['priority'],
                ]
            );
        }
    }
}
