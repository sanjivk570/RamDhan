<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create("shipment_items", function (Blueprint $table): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->unsignedBigInteger("shipment_id")->index();
            $table->unsignedBigInteger("order_item_id")->index();
            $table->unsignedBigInteger("product_id")->index();
            $table
                ->unsignedBigInteger("product_variant_id")
                ->nullable()
                ->index();
            $table->decimal("quantity", 15, 3);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("shipment_items");
    }
};
