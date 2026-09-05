<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * NOTE: The WithoutModelEvents trait is intentionally NOT used.
     * Many module seeders rely on Eloquent model "creating" events
     * (booted hooks) to auto-generate uuid and document numbers such
     * as order/invoice/po/shipment numbers. Disabling those events
     * causes "Field 'uuid' doesn't have a default value" errors.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            \App\Modules\Role\Seeders\RoleSeeder::class,
            \App\Modules\Role\Seeders\PermissionSeeder::class,
            \App\Modules\Role\Seeders\RolePermissionSeeder::class,
            \App\Modules\User\Seeders\SuperAdminSeeder::class,
            \App\Modules\Category\Seeders\CategorySeeder::class,
            \App\Modules\Product\Seeders\ProductSeeder::class,
            \App\Modules\Customer\Seeders\CustomerDemoSeeder::class,
            \App\Modules\Shipping\Seeders\ShippingDemoSeeder::class,
            \App\Modules\Supplier\Seeders\SupplierPermissionSeeder::class,
            \App\Modules\Purchase\Seeders\PurchasePermissionSeeder::class,
            \App\Modules\Slider\Seeders\SliderPermissionSeeder::class,
            \App\Modules\Slider\Seeders\SliderSeeder::class,

            // Dummy data seeders for remaining modules
            \App\Modules\Unit\Seeders\UnitSeeder::class,
            \App\Modules\Tax\Seeders\TaxSeeder::class,
            \App\Modules\Attribute\Seeders\AttributeSeeder::class,
            \App\Modules\Supplier\Seeders\SupplierSeeder::class,
            \App\Modules\ProductVariant\Seeders\ProductVariantSeeder::class,
            \App\Modules\Inventory\Seeders\InventorySeeder::class,
            \App\Modules\Promotion\Seeders\CouponSeeder::class,

            // Full lifecycle dummy data (addresses → carts → orders → fulfilment)
            \App\Modules\Customer\Seeders\CustomerAddressSeeder::class,
            \App\Modules\Cart\Seeders\CartSeeder::class,
            \App\Modules\Wishlist\Seeders\WishlistSeeder::class,
            \App\Modules\Order\Seeders\OrderSeeder::class,
            \App\Modules\Shipment\Seeders\ShipmentSeeder::class,
            \App\Modules\SalesInvoice\Seeders\SalesInvoiceSeeder::class,
            \App\Modules\SalesReturn\Seeders\SalesReturnSeeder::class,
            \App\Modules\Purchase\Seeders\PurchaseOrderSeeder::class,

            // Module-level permission seeders (idempotent)
            \App\Modules\Cart\Seeders\CartPermissionSeeder::class,
            \App\Modules\Payment\Seeders\PaymentPermissionSeeder::class,
            \App\Modules\Promotion\Seeders\PromotionPermissionSeeder::class,
            \App\Modules\SalesReturn\Seeders\SalesReturnPermissionSeeder::class,
            \App\Modules\Shipment\Seeders\ShipmentPermissionSeeder::class,
            \App\Modules\Wishlist\Seeders\WishlistPermissionSeeder::class,

            // Media (product / slider images) & supplier portal users
            \App\Modules\Media\Seeders\MediaSeeder::class,
            \App\Modules\SupplierUser\Seeders\SupplierUserSeeder::class,
        ]);
    }
}
