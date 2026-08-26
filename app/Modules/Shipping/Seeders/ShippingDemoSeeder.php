<?php

namespace App\Modules\Shipping\Seeders;

use App\Modules\Shipping\Models\ShippingMethod;
use App\Modules\Shipping\Models\ShippingRate;
use App\Modules\Shipping\Models\ShippingZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed demo shipping configuration.
 *
 * Creates delivery methods plus several zones/rates so that
 * shipping can be tested for most Indian destinations:
 *
 * - Delhi NCR            : DL/HR/UP + NCR postal codes (same-day available)
 * - North India          : northern states
 * - Mumbai / Maharashtra : MH + 400xxx postal codes
 * - Pan India            : country-wide fallback for any other pin code
 *
 * @author Sanjiv Kumar Kushwaha
 */
class ShippingDemoSeeder extends Seeder
{
    /**
     * Run the demo shipping database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        /*
         * ---------------------------------------------------------
         * Delivery methods
         * ---------------------------------------------------------
         */

        $methods = [
            [
                'code' => 'standard',
                'name' => 'Standard Delivery',
                'description' => 'Regular courier delivery.',
                'min_delivery_days' => 3,
                'max_delivery_days' => 5,
                'sort_order' => 1,
            ],

            [
                'code' => 'express',
                'name' => 'Express Delivery',
                'description' => 'Priority air express.',
                'min_delivery_days' => 1,
                'max_delivery_days' => 2,
                'sort_order' => 2,
            ],

            [
                'code' => 'sameday',
                'name' => 'Same Day Delivery',
                'description' => 'Delivered today within metro cities.',
                'min_delivery_days' => 0,
                'max_delivery_days' => 1,
                'sort_order' => 3,
            ],
        ];

        $methodIds = [];

        foreach ($methods as $data) {
            $method = ShippingMethod::firstOrCreate(
                ['code' => $data['code']],
                [
                    'uuid' => (string) Str::uuid(),
                    ...$data,
                    'is_active' => true,
                ]
            );

            $methodIds[$data['code']] = $method->id;
        }

        $this->seedZones($methodIds);
    }

    /**
     * Seed the demo zones and their rates.
     *
     * @param array<string, int> $methodIds
     * @return void
     */
    private function seedZones(array $methodIds): void
    {
        $zones = [

            /*
             * Delhi NCR already exists from earlier testing; only
             * top it up with the same-day rate below.
             */
            'delhi-ncr-topup' => [
                'existing_code' => 'DELHI_NCRs',
                'rates' => [
                    [
                        'method' => 'sameday',
                        'base_rate' => 299,
                        'per_kg_rate' => 40,
                        'free_shipping_threshold' => 4999,
                        'min_weight' => 0,
                        'max_weight' => 10,
                        'sort_order' => 3,
                    ],
                ],
            ],

            'north-india' => [
                'name' => 'North India',
                'code' => 'NORTH-INDIA',
                'description' => 'Northern states: Delhi, Haryana, UP, Punjab, Rajasthan, Uttarakhand.',
                'countries' => ['IN'],
                'states' => ['DL', 'HR', 'UP', 'PB', 'RJ', 'UK'],
                'postal_codes' => null,
                'sort_order' => 10,
                'rates' => [
                    [
                        'method' => 'standard',
                        'base_rate' => 80,
                        'per_kg_rate' => 25,
                        'free_shipping_threshold' => 1999,
                        'sort_order' => 1,
                    ],
                    [
                        'method' => 'express',
                        'base_rate' => 180,
                        'per_kg_rate' => 30,
                        'free_shipping_threshold' => 4999,
                        'sort_order' => 2,
                    ],
                ],
            ],

            'mumbai-mh' => [
                'name' => 'Mumbai / Maharashtra',
                'code' => 'MUMBAI-MH',
                'description' => 'Maharashtra state incl. Mumbai and Pune pincodes.',
                'countries' => ['IN'],
                'states' => ['MH'],
                'postal_codes' => [
                    '400001', '400002', '400003', '400005', '400007',
                    '400010', '400020', '400050', '400058', '400070',
                    '411001', '411004', '411014', '422001',
                ],
                'sort_order' => 10,
                'rates' => [
                    [
                        'method' => 'standard',
                        'base_rate' => 60,
                        'per_kg_rate' => 20,
                        'free_shipping_threshold' => 999,
                        'sort_order' => 1,
                    ],
                    [
                        'method' => 'express',
                        'base_rate' => 150,
                        'per_kg_rate' => 25,
                        'free_shipping_threshold' => 3999,
                        'sort_order' => 2,
                    ],
                    [
                        'method' => 'sameday',
                        'base_rate' => 249,
                        'per_kg_rate' => 35,
                        'free_shipping_threshold' => 4999,
                        'max_weight' => 15,
                        'sort_order' => 3,
                    ],
                ],
            ],

            'pan-india' => [
                'name' => 'Pan India',
                'code' => 'PAN-INDIA',
                'description' => 'Country-wide fallback covering any Indian destination.',
                'countries' => ['IN'],
                'states' => null,
                'postal_codes' => null,
                'sort_order' => 100,
                'rates' => [
                    [
                        'method' => 'standard',
                        'base_rate' => 100,
                        'per_kg_rate' => 25,
                        'free_shipping_threshold' => 1999,
                        'sort_order' => 1,
                    ],
                    [
                        'method' => 'express',
                        'base_rate' => 220,
                        'per_kg_rate' => 35,
                        'free_shipping_threshold' => 7999,
                        'sort_order' => 2,
                    ],
                ],
            ],
        ];

        foreach ($zones as $zone) {

            /*
             * Top-up mode: attach extra rates to an existing zone.
             */
            if (!empty($zone['existing_code'])) {
                $existing = ShippingZone::where('code', $zone['existing_code'])->first();

                if (!$existing) {
                    continue;
                }

                $this->seedRates($existing, $zone['rates'], $methodIds);

                continue;
            }

            $zoneModel = ShippingZone::firstOrCreate(
                ['code' => $zone['code']],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $zone['name'],
                    'description' => $zone['description'],
                    'countries' => $zone['countries'],
                    'states' => $zone['states'],
                    'postal_codes' => $zone['postal_codes'],
                    'is_active' => true,
                    'sort_order' => $zone['sort_order'],
                ]
            );

            $this->seedRates($zoneModel, $zone['rates'], $methodIds);
        }
    }

    /**
     * Create the given rates for a zone (idempotent).
     *
     * @param ShippingZone $zone
     * @param array<int, array<string, mixed>> $rates
     * @param array<string, int> $methodIds
     * @return void
     */
    private function seedRates(ShippingZone $zone, array $rates, array $methodIds): void
    {
        foreach ($rates as $rate) {
            ShippingRate::firstOrCreate(
                [
                    'shipping_zone_id' => $zone->id,
                    'shipping_method_id' => $methodIds[$rate['method']],
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'min_weight' => $rate['min_weight'] ?? null,
                    'max_weight' => $rate['max_weight'] ?? null,
                    'min_order_amount' => $rate['min_order_amount'] ?? null,
                    'max_order_amount' => $rate['max_order_amount'] ?? null,
                    'base_rate' => $rate['base_rate'],
                    'per_kg_rate' => $rate['per_kg_rate'],
                    'free_shipping_threshold' => $rate['free_shipping_threshold'] ?? null,
                    'is_active' => true,
                    'sort_order' => $rate['sort_order'],
                ]
            );
        }
    }
}
