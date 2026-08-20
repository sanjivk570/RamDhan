<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("product_variant_attribute_values", function (
            Blueprint $table
        ) {
            $table->id();

            /*
             * Product variant.
             */
            $table
                ->foreignId("product_variant_id")
                ->constrained("product_variants")
                ->cascadeOnDelete();

            /*
             * Attribute value.
             */
            $table
                ->foreignId("attribute_value_id")
                ->constrained("attribute_values")
                ->cascadeOnDelete();

            $table->timestamps();

            /*
             * Same attribute value cannot be
             * attached twice to one variant.
             */
            $table->unique( ['product_variant_id', 'attribute_value_id'], 'variant_attribute_value_unique' );

            /*
             * Query optimization.
             */
            $table->index("attribute_value_id");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("product_variant_attribute_values");
    }
};
