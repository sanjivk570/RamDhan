<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_invoices', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('invoice_number', 80)->unique();
            $table->unsignedBigInteger('supplier_id')->index();
            $table->unsignedBigInteger('purchase_order_id')->nullable()->index();
            $table->unsignedBigInteger('goods_receipt_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->string('status', 30)->default('draft')->index();
            $table->date('invoice_date')->index();
            $table->date('due_date')->nullable()->index();
            $table->string('supplier_invoice_number', 100)->nullable()->index();
            $table->string('currency_code', 3)->default('INR');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('due_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['supplier_id', 'status']);
            $table->index(['invoice_date', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('purchase_invoices'); }
};
