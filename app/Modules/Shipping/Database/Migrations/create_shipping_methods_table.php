<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("shipping_methods", function (Blueprint $table) {
            $table->id();

            $table->uuid("uuid")->unique();

            $table->string("name", 150);

            /*
             * Example:
             *
             * standard
             * express
             * same_day
             * pickup
             */
            $table->string("code", 50)->unique();

            $table->text("description")->nullable();

            /*
             * estimated delivery time.
             */
            $table->unsignedInteger("min_delivery_days")->default(1);

            $table->unsignedInteger("max_delivery_days")->default(5);

            $table->boolean("is_active")->default(true);

            $table->unsignedInteger("sort_order")->default(0);

            $table->softDeletes();

            $table->timestamps();

            $table->index("name");
            $table->index("is_active");
            $table->index("sort_order");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("shipping_methods");
    }
};
