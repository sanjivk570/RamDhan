<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create("payment_intents", function (Blueprint $table): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->unsignedBigInteger("order_id")->index();
            $table
                ->unsignedBigInteger("customer_id")
                ->nullable()
                ->index();
            $table->string("provider", 50);
            $table->string("method", 40);
            $table
                ->string("status", 30)
                ->default("created")
                ->index();
            $table
                ->string("provider_reference", 150)
                ->nullable()
                ->index();
            $table->decimal("amount", 15, 2);
            $table->string("currency_code", 3)->default("INR");
            $table->json("provider_payload")->nullable();
            $table->json("provider_response")->nullable();
            $table->timestamp("expires_at")->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("payment_intents");
    }
};
