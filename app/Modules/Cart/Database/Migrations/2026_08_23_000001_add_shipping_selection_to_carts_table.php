<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add shipping selection + shipping address snapshot to carts.
 *
 * After the shopper selects an address, the active shipping rate and
 * destination snapshot are stored on the cart so tax/shipping can be
 * recalculated and re-validated at every step (summary, apply, checkout).
 *
 * @package App\Modules\Cart\Database\Migrations
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table): void {
            $table->string('shipping_rate_uuid', 36)->nullable()->index();
            $table->string('shipping_method_uuid', 36)->nullable();
            $table->string('shipping_method_name', 150)->nullable();
            $table->string('shipping_method_code', 60)->nullable();
            $table->unsignedInteger('estimated_delivery_min_days')->nullable();
            $table->unsignedInteger('estimated_delivery_max_days')->nullable();
            $table->json('shipping_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table): void {
            $table->dropColumn([
                'shipping_rate_uuid',
                'shipping_method_uuid',
                'shipping_method_name',
                'shipping_method_code',
                'estimated_delivery_min_days',
                'estimated_delivery_max_days',
                'shipping_address',
            ]);
        });
    }
};