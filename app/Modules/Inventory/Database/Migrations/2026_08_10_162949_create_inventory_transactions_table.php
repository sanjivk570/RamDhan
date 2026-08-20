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
        Schema::create('inventory_transactions', function (Blueprint $table) {

            $table->id();

            /*
             * Public UUID.
             */
            $table->uuid('uuid')
                ->unique();

            /*
             * Inventory stock reference.
             */
            $table->foreignId('inventory_stock_id')
                ->constrained('inventory_stocks')
                ->cascadeOnDelete();

            /*
             * Product reference for easier reporting.
             */
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->cascadeOnDelete();

            /*
             * Transaction type.
             *
             * Examples:
             *
             * purchase
             * sale
             * return
             * adjustment
             * damage
             * cancellation
             * transfer
             */
            $table->string('type', 50);

            /*
             * Quantity moved by this transaction.
             *
             * Positive quantity is stored here.
             * Direction is represented by transaction type.
             */
            $table->decimal('quantity', 14, 4);

            /*
             * Stock before transaction.
             */
            $table->decimal('quantity_before', 14, 4);

            /*
             * Stock after transaction.
             */
            $table->decimal('quantity_after', 14, 4);

            /*
             * Optional reference information.
             *
             * Example:
             *
             * order UUID
             * purchase UUID
             * return UUID
             * adjustment reference
             */
            $table->string('reference_type', 100)
                ->nullable();

            $table->string('reference_id', 100)
                ->nullable();

            /*
             * Optional human-readable note.
             */
            $table->text('notes')
                ->nullable();

            /*
             * User who performed the inventory operation.
             */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
             * Reporting indexes.
             */
            $table->index([
                'inventory_stock_id',
                'created_at',
            ]);

            $table->index([
                'product_id',
                'created_at',
            ]);

            $table->index('type');

            $table->index([
                'reference_type',
                'reference_id',
            ]);

            /* * Variant transaction history. */ 
            $table->index([ 'product_variant_id', 'created_at', ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
