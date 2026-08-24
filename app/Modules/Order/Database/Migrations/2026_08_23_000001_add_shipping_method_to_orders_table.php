<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add shipping method snapshot to orders.
 *
 * The selected (validated) shipping rate is frozen on the order so the
 * checkout totals always match what the shopper approved.
 *
 * @package App\Modules\Order\Database\Migrations
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('shipping_rate_uuid', 36)->nullable()->index();
            $table->string('shipping_method_uuid', 36)->nullable();
            $table->string('shipping_method_name', 150)->nullable();
            $table->string('shipping_method_code', 60)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'shipping_rate_uuid',
                'shipping_method_uuid',
                'shipping_method_name',
                'shipping_method_code',
            ]);
        });
    }
};