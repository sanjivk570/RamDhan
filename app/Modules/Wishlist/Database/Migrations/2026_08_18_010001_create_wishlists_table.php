<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("wishlists", function (Blueprint $table): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->unsignedBigInteger("customer_id")->index();
            $table->unsignedBigInteger("product_id")->index();
            $table
                ->unsignedBigInteger("product_variant_id")
                ->nullable()
                ->index();
            $table->timestamps();
            $table->unique(
                ["customer_id", "product_id", "product_variant_id"],
                "wishlist_unique_item"
            );
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('set null');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("wishlists");
    }
};
