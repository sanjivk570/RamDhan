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
        Schema::create("tax_rates", function (Blueprint $table) {
            $table->id();

            /*
             * Public UUID.
             */
            $table->uuid("uuid")->unique();

            /*
             * Tax class relationship.
             */
            $table
                ->foreignId("tax_class_id")
                ->constrained("tax_classes")
                ->cascadeOnDelete();

            /*
             * Tax rate name.
             *
             * Example:
             * GST 18%
             * GST 5%
             */
            $table->string("name", 100);

            /*
             * Tax percentage.
             *
             * Example:
             * 18.00
             * 5.00
             * 0.00
             */
            $table->decimal("rate", 5, 2);

            /*
             * Country code.
             *
             * Example:
             * IN
             */
            $table->string("country_code", 2)->default("IN");

            /*
             * Optional state code.
             *
             * Useful if tax rules become
             * state-specific later.
             */
            $table->string("state_code", 10)->nullable();

            /*
             * Active/inactive status.
             */
            $table
                ->boolean("is_active")
                ->default(true)
                ->index();

            /*
             * Priority for resolving
             * applicable tax rate.
             */
            $table->unsignedInteger("priority")->default(0);

            $table->timestamps();

            /*
             * Soft delete.
             */
            $table->softDeletes();

            /*
             * Frequently used lookup.
             */
            $table->index(["tax_class_id", "is_active"]);

            $table->index(["country_code", "state_code", "is_active"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("tax_rates");
    }
};
