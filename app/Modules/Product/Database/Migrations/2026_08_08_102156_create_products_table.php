<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            $table->uuid('uuid')->unique();

            /* * Product measurement/selling unit. * * Example: * Piece, Kg, Gram, Liter, Meter, Box etc. * * Nullable because some products may not require * a measurable unit at product creation time. */
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();

            /* * Tax classification for the product. * * The tax class determines which tax configuration * should be applied to the product. */ 
            $table->foreignId('tax_class_id')->nullable()->constrained('tax_classes')->nullOnDelete();

            $table->string('name', 150);

            $table->string('slug', 180)->unique();

            $table->string('sku', 100)->unique();

            $table->text('short_description')->nullable();

            $table->longText('description')->nullable();

            $table->decimal('price', 12, 2);

            $table->decimal('compare_price', 12, 2)->nullable();

            /* * Actual purchase/acquisition cost of the product. * * Used for: * - Profit calculation * - Margin calculation * - Inventory valuation * - Reporting */ 
            $table->decimal('cost_price', 12, 2)->nullable();

            $table->boolean('is_active')->default(true);

            $table->boolean('is_featured')->default(false);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->softDeletes();

            /*
             * Indexes
             */

            $table->index('name');

            $table->index('is_active');

            $table->index('is_featured');

            $table->index('sort_order');

            $table->index('unit_id'); 
            $table->index('tax_class_id');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};