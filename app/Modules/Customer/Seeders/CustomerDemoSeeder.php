<?php

namespace App\Modules\Customer\Seeders;

use App\Modules\Customer\Models\Customer;
use App\Modules\Customer\Models\CustomerAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed a demo customer with dummy addresses.
 *
 * Useful for manually testing cart / shipping / checkout flows
 * that require an authenticated customer with saved addresses.
 *
 * Dummy login:
 *   email:    demo.customer@ramdhan.test
 *   password: Password@123
 *
 * @author Sanjiv Kumar Kushwaha
 */
class CustomerDemoSeeder extends Seeder
{
    /**
     * Run the demo customer database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        /*
         * Mobile column is unique - if some other record already owns
         * the dummy number, seed the address book without a mobile.
         */
        $mobile = '9990000001';

        if (
            Customer::where('mobile', $mobile)
                ->where('email', '!=', 'demo.customer@ramdhan.test')
                ->exists()
        ) {
            $mobile = null;
        }

        $customer = Customer::firstOrCreate(
            [
                'email' => 'demo.customer@ramdhan.test',
            ],
            [
                'uuid' => (string) Str::uuid(),

                'customer_code' => 'CUS-DEMO001',

                'first_name' => 'Demo',

                'last_name' => 'Customer',

                'country_code' => '+91',

                'mobile' => $mobile,

                // Hashed automatically by the model cast.
                'password' => 'Password@123',

                'email_verified_at' => now(),

                'mobile_verified_at' => now(),

                'is_active' => true,
            ]
        );

        /*
         * Dummy addresses. Postal codes intentionally cover different
         * shipping zones so every checkout scenario can be tested:
         *
         * - 110008 -> "Delhi NCR" zone (incl. same-day delivery)
         * - 400001 -> "Mumbai / Maharashtra" zone
         * - 560001 -> falls back to the "Pan India" zone
         */
        $addresses = [
            [
                'type' => 'shipping',
                'label' => 'Home',
                'first_name' => 'Demo',
                'last_name' => 'Customer',
                'address_line_1' => '12/3 Roop Nagar',
                'address_line_2' => 'Near Karol Bagh Metro',
                'landmark' => 'Opposite City Walk',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'state_code' => 'DL',
                'postal_code' => '110008',
                'country' => 'India',
                'country_code' => 'IN',
                'country_code_phone' => '+91',
                'phone' => '9876543210',
                'is_default' => true,
                'is_active' => true,
            ],

            [
                'type' => 'billing',
                'label' => 'Office',
                'first_name' => 'Demo',
                'last_name' => 'Customer',
                'company' => 'RamDhan Retail Pvt Ltd',
                'address_line_1' => '5th Floor, Tower B',
                'address_line_2' => 'DLF Cyber City',
                'landmark' => null,
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'state_code' => 'HR',
                'postal_code' => '122002',
                'country' => 'India',
                'country_code' => 'IN',
                'country_code_phone' => '+91',
                'phone' => '9876500000',
                'is_default' => false,
                'is_active' => true,
            ],

            [
                'type' => 'shipping',
                'label' => 'Mumbai Flat',
                'first_name' => 'Demo',
                'last_name' => 'Customer',
                'address_line_1' => 'Flat 402, Sea Breeze',
                'address_line_2' => 'Colaba Causeway',
                'landmark' => 'Behind Gateway Hotel',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'state_code' => 'MH',
                'postal_code' => '400001',
                'country' => 'India',
                'country_code' => 'IN',
                'country_code_phone' => '+91',
                'phone' => '9811100000',
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($addresses as $data) {
            CustomerAddress::firstOrCreate(
                [
                    'customer_id' => $customer->id,
                    'postal_code' => $data['postal_code'],
                    'label' => $data['label'],
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    ...$data,
                ]
            );
        }
    }
}
