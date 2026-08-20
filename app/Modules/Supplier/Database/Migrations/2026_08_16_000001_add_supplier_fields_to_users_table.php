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
                $table->unsignedBigInteger('supplier_id')->nullable()->after('uuid');
                $table->index('supplier_id');
            }

            if (!Schema::hasColumn('users', 'user_type')) {
                $table->string('user_type', 30)->default('staff')->after('supplier_id');
                $table->index('user_type');
            }

            if (!Schema::hasColumn('users', 'is_primary_supplier_user')) {
                $table->boolean('is_primary_supplier_user')->default(false)->after('user_type');
                $table->index('is_primary_supplier_user');
            }

            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }

            if (Schema::hasColumn('users', 'is_primary_supplier_user')) {
                $table->dropIndex(['is_primary_supplier_user']);
                $table->dropColumn('is_primary_supplier_user');
            }

            if (Schema::hasColumn('users', 'user_type')) {
                $table->dropIndex(['user_type']);
                $table->dropColumn('user_type');
            }

            if (Schema::hasColumn('users', 'supplier_id')) {
                $table->dropIndex(['supplier_id']);
                $table->dropColumn('supplier_id');
            }
        });
    }
};
