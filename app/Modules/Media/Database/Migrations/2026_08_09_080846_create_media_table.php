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
        Schema::create("media", function (Blueprint $table) {
            $table->id();

            $table->uuid("uuid")->unique();

            // Polymorphic relationship
            $table->string("mediable_type", 100)->nullable();
            $table->unsignedBigInteger("mediable_id")->nullable();

            // Media collection - Example: products, categories, avatars, documents
            $table->string('collection', 100)->default('default');

            $table->string("original_name");
            $table->string("file_name");
            $table->string("disk")->default("public");
            $table->string("path");
            $table->string("mime_type");
            $table->unsignedBigInteger("size");
            $table->string("title")->nullable();
            $table->string("alt_text")->nullable();
            $table->text("description")->nullable();
            $table->string("type", 50)->default("other");
            $table->unsignedInteger("sort_order")->default(0);
            $table->boolean("is_primary")->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(["mediable_type", "mediable_id"]);

            //Collection lookup.
            $table->index(['mediable_type', 'mediable_id', 'collection']);

            $table->index(["type", "created_at"]);
            $table->index(["is_primary"]);

            //Primary media lookup.
            $table->index(['mediable_type', 'mediable_id', 'collection', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("media");
    }
};
