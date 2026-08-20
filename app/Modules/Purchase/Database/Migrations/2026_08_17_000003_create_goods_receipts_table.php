<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('goods_receipts', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('grn_number', 50)->unique();
            $table->unsignedBigInteger('purchase_order_id')->index();
            $table->unsignedBigInteger('supplier_id')->index();
            $table->unsignedBigInteger('received_by')->nullable()->index();
            $table->unsignedBigInteger('posted_by')->nullable()->index();
            $table->string('status', 20)->default('draft')->index();
            $table->date('receipt_date')->index();
            $table->date('supplier_document_date')->nullable();
            $table->string('supplier_document_number', 100)->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->unsignedBigInteger('voided_by')->nullable()->index();
            $table->text('void_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['purchase_order_id', 'status']);
            $table->index(['supplier_id', 'receipt_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};
