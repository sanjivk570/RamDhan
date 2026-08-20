<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("product_variants", function (Blueprint $table) {
            $table->id();

            /*
             * Public UUID.
             */
            $table->uuid("uuid")->unique();

            /*
             * Parent product.
             */
            $table
                ->foreignId("product_id")
                ->constrained("products")
                ->cascadeOnDelete();

            /*
             * Variant display name.
             *
             * Example:
             * iPhone 16 Pro - 128GB - Black
             */
            $table->string("name", 180);

            /*
             * Variant SKU.
             */
            $table->string("sku", 100)->unique();

            /*
             * Variant pricing.
             *
             * Nullable so variant can optionally
             * inherit product price.
             */
            $table->decimal("price", 12, 2)->nullable();

            $table->decimal("compare_price", 12, 2)->nullable();

            $table->decimal("cost_price", 12, 2)->nullable();

            /*
             * Default variant.
             *
             * One product should have only one
             * default variant at application level.
             */
            $table->boolean("is_default")->default(false);

            /*
             * Variant active status.
             */
            $table->boolean("is_active")->default(true);

            /*
             * Display ordering.
             */
            $table->unsignedInteger("sort_order")->default(0);

            $table->timestamps();

            /*
             * Soft delete.
             */
            $table->softDeletes();

            /*
             * Frequently used indexes.
             */
            $table->index(["product_id", "is_active"]);

            $table->index(["product_id", "is_default"]);

            $table->index(["product_id", "sort_order"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("product_variants");
    }
};
