<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create("coupons", function (Blueprint $table): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->string("code", 100)->unique();
            $table->string("name", 200);
            $table->string("discount_type", 20);
            $table->decimal("discount_value", 15, 2);
            $table->decimal("maximum_discount", 15, 2)->nullable();
            $table->decimal("minimum_order_amount", 15, 2)->default(0);
            $table->unsignedInteger("usage_limit")->nullable();
            $table->unsignedInteger("per_customer_limit")->nullable();
            $table->unsignedInteger("used_count")->default(0);
            $table->timestamp("starts_at")->nullable();
            $table->timestamp("ends_at")->nullable();
            $table
                ->boolean("is_active")
                ->default(true)
                ->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("coupons");
    }
};
