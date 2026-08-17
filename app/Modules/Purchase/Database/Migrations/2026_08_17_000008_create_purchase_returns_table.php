<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_returns', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('return_number', 80)->unique();
            $table->unsignedBigInteger('supplier_id')->index();
            $table->unsignedBigInteger('goods_receipt_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->string('status', 30)->default('draft')->index();
            $table->date('return_date')->index();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency_code', 3)->default('INR');
            $table->text('reason')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['supplier_id', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('purchase_returns'); }
};
