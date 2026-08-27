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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            // Display name - Example: Home Hero Slider
            $table->string('name', 150);

            // Unique short identifier - Example: home_hero
            $table->string('code', 100)->unique();

            // Placement/location where the slider is shown - Example: home
            $table->string('placement', 100)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('uuid');
            $table->index('code');
            $table->index('placement');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};