<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create("sales_invoices", function (Blueprint $table): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->string("invoice_number", 80)->unique();
            $table
                ->unsignedBigInteger("order_id")
                ->unique()
                ->index();
            $table
                ->unsignedBigInteger("customer_id")
                ->nullable()
                ->index();
            $table
                ->string("status", 30)
                ->default("issued")
                ->index();
            $table->date("invoice_date");
            $table->date("due_date")->nullable();
            $table->string("currency_code", 3)->default("INR");
            $table->decimal("subtotal", 15, 2)->default(0);
            $table->decimal("discount_amount", 15, 2)->default(0);
            $table->decimal("tax_amount", 15, 2)->default(0);
            $table->decimal("shipping_amount", 15, 2)->default(0);
            $table->decimal("grand_total", 15, 2)->default(0);
            $table->decimal("paid_amount", 15, 2)->default(0);
            $table->decimal("due_amount", 15, 2)->default(0);
            $table->json("billing_address")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("sales_invoices");
    }
};
