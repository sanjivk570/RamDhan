<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_stocks', function (Blueprint $table) {

            $table->id();

            /*
             * Public UUID.
             */
            $table->uuid('uuid')
                ->unique();

            /*
             * Product reference.
             */
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->cascadeOnDelete();

            /*
             * Current physical stock quantity.
             *
             * Decimal is intentionally used instead of integer
             * because some products may use measurable units
             * such as Kg, Gram, Liter, Meter, etc.
             */
            $table->decimal('quantity', 14, 4)
                ->default(0);

            /*
             * Quantity currently reserved for orders.
             */
            $table->decimal('reserved_quantity', 14, 4)
                ->default(0);

            /*
             * Optional inventory-specific low stock threshold.
             */
            $table->decimal('low_stock_threshold', 14, 4)
                ->nullable();

            /*
             * Inventory status.
             */
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            /*
             * One inventory record per product.
             *
             * Later, when variants are introduced,
             * this structure will be extended to support
             * variant-level inventory.
             */
            //$table->unique('product_id');

            /* * Product is frequently used for filtering * and relationships, but it is NOT unique. */
            $table->index( 'product_id', 'inventory_stocks_product_id_index' );

            /* * One inventory stock record per product variant. */ 
            $table->unique( 'product_variant_id', 'inventory_stocks_product_variant_id_unique' );

            /*
             * Frequently used indexes.
             */
            $table->index('is_active');

            //$table->index('product_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stocks');
    }
};
