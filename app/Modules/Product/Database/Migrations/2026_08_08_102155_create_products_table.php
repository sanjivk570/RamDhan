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

            $table->string('name', 150);

            $table->string('slug', 180)->unique();

            $table->string('sku', 100)->unique();

            $table->text('short_description')->nullable();

            $table->longText('description')->nullable();

            $table->decimal('price', 12, 2);

            $table->decimal('compare_price', 12, 2)->nullable();

            $table->unsignedInteger('stock_quantity')->default(0);

            $table->unsignedInteger('low_stock_threshold')->default(5);

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

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};