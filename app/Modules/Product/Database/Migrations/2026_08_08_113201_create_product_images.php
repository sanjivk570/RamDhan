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
        Schema::create('product_images', function (Blueprint $table) {

            $table->id();

            /*
             * Public UUID.
             */
            $table->uuid('uuid')->unique();

            /*
             * Product relationship.
             *
             * products.id = BIGINT
             */
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            /*
             * Stored image path.
             *
             * Example:
             * products/iphone-16-pro/image-01.webp
             */
            $table->string('image_path');

            /*
             * Optional image URL.
             *
             * Useful if later images are stored
             * on S3/CDN or another storage provider.
             */
            $table->string('image_url')->nullable();

            /*
             * Image alt text.
             */
            $table->string('alt_text')->nullable();

            /*
             * Display order.
             */
            $table->unsignedInteger('sort_order')->default(0);

            /*
             * Primary product image.
             */
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            /*
             * Soft delete support.
             */
            $table->softDeletes();

            /*
             * Frequently used query:
             * product_id + sort_order
             */
            $table->index(['product_id', 'sort_order']);

            /*
             * Frequently used query:
             * primary image of a product.
             */
            $table->index(['product_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};