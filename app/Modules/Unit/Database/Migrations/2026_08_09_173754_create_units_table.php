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
        Schema::create("units", function (Blueprint $table) {
            $table->id();

            /*
             * Public UUID.
             */
            $table->uuid("uuid")->unique();

            /*
             * Unit name.
             *
             * Examples:
             * Piece
             * Kilogram
             * Liter
             * Meter
             */
            $table->string("name", 100)->index();

            /*
             * Unique unit code.
             *
             * Examples:
             * PCS
             * KG
             * L
             * M
             */
            $table->string("code", 30)->unique();

            /*
             * Display symbol.
             *
             * Examples:
             * pcs
             * kg
             * L
             * m
             */
            $table->string("symbol", 20)->nullable();

            /*
             * Number of decimal places supported
             * by this unit.
             *
             * Piece     = 0
             * Kilogram  = 3
             * Liter     = 3
             */
            $table->unsignedTinyInteger("decimal_places")->default(0);

            /*
             * Whether this unit is currently active.
             */
            $table
                ->boolean("is_active")
                ->default(true)
                ->index();

            /*
             * Display ordering.
             */
            $table
                ->unsignedInteger("sort_order")
                ->default(0)
                ->index();

            $table->timestamps();

            /*
             * Soft delete support.
             */
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("units");
    }
};
