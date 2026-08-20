<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create("order_status_histories", function (
            Blueprint $table
        ): void {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->unsignedBigInteger("order_id")->index();
            $table->string("from_status", 30)->nullable();
            $table->string("to_status", 30);
            $table
                ->unsignedBigInteger("changed_by")
                ->nullable()
                ->index();
            $table->string("source", 30)->default("system");
            $table->text("note")->nullable();
            $table->timestamps();
            $table->foreign("order_id")->references("id")->on("orders")->onDelete("cascade");
            $table->foreign("changed_by")->references("id")->on("users")->onDelete("set null");
        });
    }
    public function down(): void
    {
        Schema::dropIfExists("order_status_histories");
    }
};
