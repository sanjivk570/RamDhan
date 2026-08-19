<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create("sales_returns", function (Blueprint $table): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->string("return_number", 80)->unique();
            $table->unsignedBigInteger("order_id")->index();
            $table
                ->unsignedBigInteger("customer_id")
                ->nullable()
                ->index();
            $table
                ->string("status", 30)
                ->default("requested")
                ->index();
            $table
                ->string("refund_status", 30)
                ->default("pending")
                ->index();
            $table->decimal("total_amount", 15, 2)->default(0);
            $table->string("reason", 500)->nullable();
            $table->text("customer_note")->nullable();
            $table->text("admin_note")->nullable();
            $table
                ->unsignedBigInteger("created_by")
                ->nullable()
                ->index();
            $table
                ->unsignedBigInteger("processed_by")
                ->nullable()
                ->index();
            $table->timestamp("approved_at")->nullable();
            $table->timestamp("rejected_at")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("sales_returns");
    }
};
