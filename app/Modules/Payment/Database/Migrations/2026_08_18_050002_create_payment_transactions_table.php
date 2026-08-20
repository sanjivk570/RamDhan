<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create("payment_transactions", function (
            Blueprint $table
        ): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table
                ->unsignedBigInteger("payment_intent_id")
                ->nullable()
                ->index();
            $table->unsignedBigInteger("order_id")->index();
            $table->string("provider", 50);
            $table
                ->string("transaction_type", 30)
                ->default("payment")
                ->index();
            $table
                ->string("status", 30)
                ->default("pending")
                ->index();
            $table
                ->string("provider_transaction_id", 180)
                ->nullable()
                ->index();
            $table->decimal("amount", 15, 2);
            $table->string("currency_code", 3)->default("INR");
            $table->string("payment_method", 50)->nullable();
            $table
                ->string("reference_number", 150)
                ->nullable()
                ->index();
            $table->json("payload")->nullable();
            $table->text("failure_reason")->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('payment_intent_id')->references('id')->on('payment_intents')->onDelete('set null');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("payment_transactions");
    }
};
