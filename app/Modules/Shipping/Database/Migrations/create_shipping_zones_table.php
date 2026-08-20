<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("shipping_zones", function (Blueprint $table) {
            $table->id();

            $table->uuid("uuid")->unique();

            $table->string("name", 150);

            $table->string("code", 50)->unique();

            $table->text("description")->nullable();

            /*
             * Example:
             *
             * IN
             * IN
             *
             * JSON allows multiple countries/states/postal codes.
             */
            $table->json("countries")->nullable();

            $table->json("states")->nullable();

            $table->json("postal_codes")->nullable();

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
        Schema::dropIfExists("shipping_zones");
    }
};
