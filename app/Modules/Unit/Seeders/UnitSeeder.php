<?php

declare(strict_types=1);

namespace App\Modules\Unit\Seeders;

use App\Modules\Unit\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed default units of measurement.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'code' => 'pcs', 'symbol' => 'pc', 'decimal_places' => 0, 'sort_order' => 1],
            ['name' => 'Kilogram', 'code' => 'kg', 'symbol' => 'kg', 'decimal_places' => 3, 'sort_order' => 2],
            ['name' => 'Gram', 'code' => 'g', 'symbol' => 'g', 'decimal_places' => 0, 'sort_order' => 3],
            ['name' => 'Litre', 'code' => 'ltr', 'symbol' => 'L', 'decimal_places' => 2, 'sort_order' => 4],
            ['name' => 'Metre', 'code' => 'mtr', 'symbol' => 'm', 'decimal_places' => 2, 'sort_order' => 5],
            ['name' => 'Box', 'code' => 'box', 'symbol' => 'bx', 'decimal_places' => 0, 'sort_order' => 6],
            ['name' => 'Dozen', 'code' => 'dz', 'symbol' => 'dz', 'decimal_places' => 0, 'sort_order' => 7],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['code' => $unit['code']], $unit + ['uuid' => (string) Str::uuid(), 'is_active' => true]);
        }
    }
}
