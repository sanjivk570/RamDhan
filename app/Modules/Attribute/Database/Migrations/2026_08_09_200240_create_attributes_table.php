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
        Schema::create("attributes", function (Blueprint $table) {
            $table->id();

            /*
             * Public UUID.
             */
            $table->uuid("uuid")->unique();

            /*
             * Attribute name.
             *
             * Example:
             * Color
             * Size
             * Material
             */
            $table->string("name", 100);

            /*
             * URL / API friendly identifier.
             *
             * Example:
             * color
             * size
             */
            $table->string("slug", 120)->unique();

            /*
             * Attribute input/display type.
             *
             * Examples:
             * select
             * color
             * text
             * number
             */
            $table->string("type", 30)->default("select");

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
             * Frequently used indexes.
             */
            $table->index("name");
            $table->index("type");
            $table->index("is_active");
            $table->index("sort_order");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("attributes");
    }
};
