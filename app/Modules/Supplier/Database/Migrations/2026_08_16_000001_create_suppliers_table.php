<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("suppliers", function (Blueprint $table) {
            $table->id();

            $table->uuid("uuid")->unique();

            /*
             * Internal supplier code.
             *
             * Example:
             * SUP-000001
             */
            $table->string("supplier_code", 50)->unique();

            /*
             * Business / company information.
             */
            $table->string("company_name", 200);

            $table->string("contact_person", 150)->nullable();

            /*
             * Contact information.
             */
            $table->string("email", 120)->nullable();

            $table->string("country_code", 10)->nullable();

            $table->string("mobile", 30)->nullable();

            $table->string("alternate_mobile", 30)->nullable();

            $table->string("website", 255)->nullable();

            /*
             * Tax / legal information.
             */
            $table->string("gstin", 30)->nullable();

            $table->string("pan", 20)->nullable();

            /*
             * Commercial information.
             */
            $table->unsignedInteger("payment_terms_days")->default(0);

            $table->decimal("credit_limit", 15, 2)->default(0);

            /*
             * Additional information.
             */
            $table->text("notes")->nullable();

            /*
             * Supplier status.
             */
            $table->boolean("is_active")->default(true);

            /*
             * Soft deletion.
             */
            $table->softDeletes();

            $table->timestamps();

            /*
             * Indexes.
             */
            $table->index("company_name");
            $table->index("contact_person");
            $table->index("email");
            $table->index("mobile");
            $table->index("gstin");
            $table->index("pan");
            $table->index("is_active");
            $table->index("created_at");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("suppliers");
    }
};
