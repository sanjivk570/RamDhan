<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create("coupon_redemptions", function (Blueprint $table): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->unsignedBigInteger("coupon_id")->index();
            $table
                ->unsignedBigInteger("customer_id")
                ->nullable()
                ->index();
            $table
                ->unsignedBigInteger("order_id")
                ->nullable()
                ->index();
            $table->decimal("discount_amount", 15, 2);
            $table->timestamps();
            $table->unique(["coupon_id", "order_id"], "coupon_order_unique");
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("coupon_redemptions");
    }
};
