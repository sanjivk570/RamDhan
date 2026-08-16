<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (!Schema::hasColumn('users', 'supplier_id')) {
                return;
            }

            // Indexes are intentionally non-unique because one supplier can have many users.
            $table->index(['supplier_id', 'user_type', 'is_active'], 'users_supplier_type_active_idx');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            try {
                $table->dropIndex('users_supplier_type_active_idx');
            } catch (Throwable) {
                // Keep rollback safe when the index was already removed.
            }
        });
    }
};
