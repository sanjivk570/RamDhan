<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create("payment_refunds", function (Blueprint $table): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->unsignedBigInteger("order_id")->index();
            $table
                ->unsignedBigInteger("payment_transaction_id")
                ->nullable()
                ->index();
            $table->string("provider", 50)->nullable();
            $table
                ->string("status", 30)
                ->default("pending")
                ->index();
            $table
                ->string("provider_refund_id", 180)
                ->nullable()
                ->index();
            $table->decimal("amount", 15, 2);
            $table->string("currency_code", 3)->default("INR");
            $table->string("reason", 500)->nullable();
            $table->json("payload")->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("payment_refunds");
    }
};
