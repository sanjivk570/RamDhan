<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_payments', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('payment_number', 80)->unique();
            $table->unsignedBigInteger('supplier_id')->index();
            $table->unsignedBigInteger('purchase_invoice_id')->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->string('status', 30)->default('posted')->index();
            $table->date('payment_date')->index();
            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 3)->default('INR');
            $table->string('payment_method', 40)->index();
            $table->string('reference_number', 120)->nullable()->index();
            $table->string('bank_account', 150)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['supplier_id', 'payment_date']);
            $table->index(['purchase_invoice_id', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('purchase_payments'); }
};
