<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("shipping_rates", function (Blueprint $table) {
            $table->id();

            $table->uuid("uuid")->unique();

            $table->unsignedBigInteger("shipping_zone_id");

            $table->unsignedBigInteger("shipping_method_id");

            /*
             * Weight condition.
             *
             * NULL = unlimited.
             */
            $table->decimal("min_weight", 12, 3)->nullable();

            $table->decimal("max_weight", 12, 3)->nullable();

            /*
             * Order amount condition.
             */
            $table->decimal("min_order_amount", 15, 2)->nullable();

            $table->decimal("max_order_amount", 15, 2)->nullable();

            /*
             * Base delivery charge.
             */
            $table->decimal("base_rate", 15, 2)->default(0);

            /*
             * Additional charge per KG.
             */
            $table->decimal("per_kg_rate", 15, 2)->default(0);

            /*
             * If order amount reaches this value,
             * shipping becomes free.
             *
             * NULL = disabled.
             */
            $table->decimal("free_shipping_threshold", 15, 2)->nullable();

            $table->boolean("is_active")->default(true);

            $table->unsignedInteger("sort_order")->default(0);

            $table->softDeletes();

            $table->timestamps();

            $table->index("shipping_zone_id");
            $table->index("shipping_method_id");
            $table->index("is_active");
            $table->index("sort_order");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("shipping_rates");
    }
};
