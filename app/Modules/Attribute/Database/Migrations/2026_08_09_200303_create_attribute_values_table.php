<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("attribute_values", function (Blueprint $table) {
            $table->id();

            /*
             * Public UUID.
             */
            $table->uuid("uuid")->unique();

            /*
             * Parent attribute.
             *
             * attributes.id = BIGINT
             */
            $table
                ->foreignId("attribute_id")
                ->constrained("attributes")
                ->cascadeOnDelete();

            /*
             * Actual value.
             *
             * Example:
             * Red
             * Blue
             * Large
             */
            $table->string("value", 100);

            /*
             * API / URL friendly value.
             *
             * Example:
             * red
             * blue
             * large
             */
            $table->string("slug", 120);

            /*
             * Optional display value.
             *
             * Useful for color attributes.
             *
             * Example:
             * #FF0000
             */
            $table->string("display_value", 100)->nullable();

            /*
             * Display ordering.
             */
            $table->unsignedInteger("sort_order")->default(0);

            /*
             * Active/inactive status.
             */
            $table->boolean("is_active")->default(true);

            $table->timestamps();

            /*
             * Soft delete support.
             */
            $table->softDeletes();

            /*
             * Indexes.
             */
            $table->index(["attribute_id", "sort_order"]);

            $table->index(["attribute_id", "is_active"]);

            /*
             * Same attribute should not have
             * duplicate value slugs.
             */
            $table->unique(["attribute_id", "slug"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("attribute_values");
    }
};
