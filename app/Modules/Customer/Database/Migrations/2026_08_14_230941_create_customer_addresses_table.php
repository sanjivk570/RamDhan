<?php

// declare(strict_types=1);

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration {
//     public function up(): void
//     {
//         Schema::create("customer_addresses", function (Blueprint $table) {
//             $table->id();

//             $table->uuid("uuid")->unique();

//             $table
//                 ->foreignId("customer_id")
//                 ->constrained("customers")
//                 ->cascadeOnDelete();

//             $table->string("label", 50)->nullable();

//             $table->string("first_name", 100);

//             $table->string("last_name", 100)->nullable();

//             $table->string("company_name", 150)->nullable();

//             $table->string("phone", 30)->nullable();

//             $table->string("address_line_1", 255);

//             $table->string("address_line_2", 255)->nullable();

//             $table->string("landmark", 255)->nullable();

//             $table->string("city", 100);

//             $table->string("state", 100);

//             $table->string("state_code", 20)->nullable();

//             $table->string("postal_code", 20);

//             $table->string("country_code", 10)->default("IN");

//             $table->decimal("latitude", 10, 7)->nullable();

//             $table->decimal("longitude", 10, 7)->nullable();

//             $table->boolean("is_default_shipping")->default(false);

//             $table->boolean("is_default_billing")->default(false);

//             $table->timestamps();

//             $table->softDeletes();

//             $table->index(["customer_id", "is_default_shipping"]);

//             $table->index(["customer_id", "is_default_billing"]);
//         });
//     }

//     public function down(): void
//     {
//         Schema::dropIfExists("customer_addresses");
//     }
// };


declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("customer_addresses", function (Blueprint $table) {
            $table->id();

            $table->uuid("uuid")->unique();

            $table
                ->foreignId("customer_id")
                ->constrained("customers")
                ->cascadeOnDelete();

            /*
             * Address type:
             * shipping / billing
             */
            $table->string("type", 20)->default("shipping");

            $table->string("label", 50)->nullable();

            $table->string("first_name", 100);

            $table->string("last_name", 100)->nullable();

            $table->string("company", 150)->nullable();

            $table->string("address_line_1", 255);

            $table->string("address_line_2", 255)->nullable();

            $table->string("landmark", 255)->nullable();

            $table->string("city", 100);

            $table->string("state", 100);

            $table->string("state_code", 20)->nullable();

            $table->string("postal_code", 20);

            $table->string("country", 100)->default("India");

            $table->string("country_code", 10)->default("IN");

            $table->string("country_code_phone", 10)->nullable();

            $table->string("phone", 30)->nullable();

            $table->boolean("is_default")->default(false);

            $table->boolean("is_active")->default(true);

            $table->timestamps();

            $table->softDeletes();

            $table->index(["customer_id", "type", "is_active"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("customer_addresses");
    }
};
