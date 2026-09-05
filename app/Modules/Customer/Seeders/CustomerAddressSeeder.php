<?php

declare(strict_types=1);

namespace App\Modules\Customer\Seeders;

use App\Modules\Customer\Models\Customer;
use App\Modules\Customer\Models\CustomerAddress;
use Illuminate\Database\Seeder;

/**
 * Seed demo customer addresses.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class CustomerAddressSeeder extends Seeder
{
    public function run(): void
    {
        Customer::query()->chunkById(100, function ($customers): void {
            foreach ($customers as $index => $customer) {
                $existing = CustomerAddress::withTrashed()
                    ->where('customer_id', $customer->id)
                    ->exists();

                if ($existing) {
                    continue;
                }

                CustomerAddress::create([
                    'customer_id' => $customer->id,
                    'type' => 'both',
                    'label' => 'Home',
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'address_line_1' => 'House ' . (10 + $index) . ', Model Town',
                    'city' => 'Ludhiana',
                    'state' => 'Punjab',
                    'state_code' => 'PB',
                    'postal_code' => '141002',
                    'country' => 'India',
                    'country_code' => 'IN',
                    'country_code_phone' => '+91',
                    'phone' => $customer->mobile ?? '9876500000',
                    'is_default' => true,
                    'is_active' => true,
                ]);
            }
        });
    }
}
