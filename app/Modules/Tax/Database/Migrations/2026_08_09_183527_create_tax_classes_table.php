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
        Schema::create("tax_classes", function (Blueprint $table) {
            $table->id();

            /*
             * Public UUID.
             */
            $table->uuid("uuid")->unique();

            /*
             * Tax class name.
             *
             * Example:
             * GST Standard
             * GST Reduced
             * GST Zero
             */
            $table->string("name", 100)->index();

            /*
             * Unique internal/business code.
             *
             * Example:
             * GST_STANDARD
             * GST_REDUCED
             * GST_ZERO
             */
            $table->string("code", 50)->unique();

            /*
             * Optional description.
             */
            $table->text("description")->nullable();

            /*
             * Active/inactive status.
             */
            $table
                ->boolean("is_active")
                ->default(true)
                ->index();

            /*
             * Display ordering.
             */
            $table
                ->unsignedInteger("sort_order")
                ->default(0)
                ->index();

            $table->timestamps();

            /*
             * Soft delete.
             */
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("tax_classes");
    }
};
