<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create("sales_return_items", function (Blueprint $table): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->unsignedBigInteger("sales_return_id")->index();
            $table->unsignedBigInteger("order_item_id")->index();
            $table->unsignedBigInteger("product_id")->index();
            $table
                ->unsignedBigInteger("product_variant_id")
                ->nullable()
                ->index();
            $table->decimal("quantity", 15, 3);
            $table->decimal("unit_price", 15, 2);
            $table->decimal("line_total", 15, 2)->default(0);
            $table->string("reason", 500)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("sales_return_items");
    }
};
