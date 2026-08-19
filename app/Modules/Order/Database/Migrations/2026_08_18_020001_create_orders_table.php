<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('orders', function (Blueprint $table): void {
        $table->id(); $table->uuid('uuid')->unique(); $table->string('order_number',80)->unique();
        $table->unsignedBigInteger('customer_id')->nullable()->index(); $table->string('guest_token',120)->nullable()->index();
        $table->string('customer_email', 120)->index(); $table->string('customer_name',200)->nullable(); $table->string('customer_phone',40)->nullable();
        $table->string('status',30)->default('pending')->index(); $table->string('payment_status',30)->default('pending')->index(); $table->string('fulfillment_status',30)->default('unfulfilled')->index();
        $table->string('currency_code',3)->default('INR');
        $table->decimal('subtotal',15,2)->default(0); $table->decimal('discount_amount',15,2)->default(0); $table->decimal('tax_amount',15,2)->default(0);
        $table->decimal('shipping_amount',15,2)->default(0); $table->decimal('grand_total',15,2)->default(0);
        $table->string('coupon_code',100)->nullable()->index(); $table->string('payment_method',40)->nullable();
        $table->json('billing_address')->nullable(); $table->json('shipping_address')->nullable();
        $table->text('customer_note')->nullable(); $table->text('internal_note')->nullable();
        $table->timestamp('placed_at')->nullable(); $table->timestamp('cancelled_at')->nullable(); $table->unsignedBigInteger('cancelled_by')->nullable()->index(); $table->text('cancellation_reason')->nullable();
        $table->timestamps(); $table->softDeletes(); $table->index(['created_at','status']);
    }); }
    public function down(): void { Schema::dropIfExists('orders'); }
};
