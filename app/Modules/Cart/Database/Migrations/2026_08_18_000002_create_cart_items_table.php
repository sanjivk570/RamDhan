<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cart_items', function (Blueprint $table): void {
            $table->id(); $table->uuid('uuid')->unique(); $table->unsignedBigInteger('cart_id')->index();
            $table->unsignedBigInteger('product_id')->index(); $table->unsignedBigInteger('tax_class_id')->nullable()->index(); $table->unsignedBigInteger('product_variant_id')->nullable()->index();
            $table->string('sku',100)->nullable()->index(); $table->string('product_name',255); $table->string('variant_name',255)->nullable();
            $table->decimal('quantity',15,3); $table->decimal('unit_price',15,2); $table->decimal('compare_price',15,2)->default(0);
            $table->decimal('discount_amount',15,2)->default(0); $table->decimal('tax_rate',8,4)->default(0); $table->decimal('tax_amount',15,2)->default(0);
            $table->decimal('line_subtotal',15,2)->default(0); $table->decimal('line_total',15,2)->default(0); $table->timestamps();
            $table->index(['cart_id','product_id','product_variant_id'], 'cart_item_product_idx');
        });
    }
    public function down(): void { Schema::dropIfExists('cart_items'); }
};
