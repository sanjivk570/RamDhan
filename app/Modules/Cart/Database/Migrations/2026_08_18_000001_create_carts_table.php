<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('carts', function (Blueprint $table): void {
            $table->id(); $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('guest_token', 120)->nullable()->unique();
            $table->string('status', 20)->default('active')->index();
            $table->string('currency_code',3)->default('INR');
            $table->string('coupon_code',100)->nullable()->index();
            $table->decimal('subtotal',15,2)->default(0);
            $table->decimal('discount_amount',15,2)->default(0);
            $table->decimal('tax_amount',15,2)->default(0);
            $table->decimal('shipping_amount',15,2)->default(0);
            $table->decimal('grand_total',15,2)->default(0);
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps(); $table->softDeletes();
            $table->index(['customer_id','status']);
        });
    }
    public function down(): void { Schema::dropIfExists('carts'); }
};
