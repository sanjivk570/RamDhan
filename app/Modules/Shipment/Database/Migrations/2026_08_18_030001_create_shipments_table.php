<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create("shipments", function (Blueprint $table): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->string("shipment_number", 80)->unique();
            $table->unsignedBigInteger("order_id")->index();
            $table
                ->string("status", 30)
                ->default("pending")
                ->index();
            $table->string("carrier", 100)->nullable();
            $table->string("service", 100)->nullable();
            $table
                ->string("tracking_number", 150)
                ->nullable()
                ->index();
            $table->string("tracking_url", 500)->nullable();
            $table->json("shipping_address")->nullable();
            $table->timestamp("shipped_at")->nullable();
            $table->timestamp("delivered_at")->nullable();
            $table->timestamp("cancelled_at")->nullable();
            $table
                ->unsignedBigInteger("created_by")
                ->nullable()
                ->index();
            $table->text("notes")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("shipments");
    }
};
