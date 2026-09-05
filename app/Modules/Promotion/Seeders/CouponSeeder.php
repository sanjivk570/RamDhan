<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Seeders;

use App\Modules\Promotion\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed demo coupons.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome 10% Off',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'maximum_discount' => 500,
                'minimum_order_amount' => 500,
                'usage_limit' => 1000,
                'per_customer_limit' => 1,
            ],
            [
                'code' => 'FLAT100',
                'name' => 'Flat Rs. 100 Off',
                'discount_type' => 'fixed',
                'discount_value' => 100,
                'maximum_discount' => null,
                'minimum_order_amount' => 999,
                'usage_limit' => 500,
                'per_customer_limit' => 2,
            ],
            [
                'code' => 'FESTIVE20',
                'name' => 'Festive Season 20% Off',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'maximum_discount' => 1500,
                'minimum_order_amount' => 1999,
                'usage_limit' => 200,
                'per_customer_limit' => 1,
            ],
        ];

        foreach ($coupons as $data) {
            Coupon::firstOrCreate(
                ['code' => $data['code']],
                $data + [
                    'uuid' => (string) Str::uuid(),
                    'used_count' => 0,
                    'starts_at' => $now->copy()->subDays(7),
                    'ends_at' => $now->copy()->addDays(90),
                    'is_active' => true,
                ]
            );
        }
    }
}
