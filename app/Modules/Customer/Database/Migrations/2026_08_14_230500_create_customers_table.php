<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("customers", function (Blueprint $table) {
            $table->id();

            $table->uuid("uuid")->unique();

            $table->string("customer_code", 30)->unique();

            $table->string("first_name", 100);

            $table->string("last_name", 100)->nullable();

            $table->string("email", 120)->unique();

            $table->string("country_code", 10)->nullable();

            $table->string("mobile", 30)->nullable()->unique();

            $table->string("avatar")->nullable();

            $table->string("password");

            $table->timestamp("email_verified_at")->nullable();

            $table->timestamp("mobile_verified_at")->nullable();

            $table->timestamp("last_login_at")->nullable();

            $table->boolean("is_active")->default(true);

            $table->rememberToken();

            $table->timestamps();

            $table->softDeletes();

            $table->index("is_active");
            $table->index("created_at");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("customers");
    }
};
