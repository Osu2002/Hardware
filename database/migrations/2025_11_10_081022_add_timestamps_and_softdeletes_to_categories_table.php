<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Add created_at/updated_at if they don't exist (safe checks)
            if (!Schema::hasColumn('categories', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('featured');
            }
            if (!Schema::hasColumn('categories', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
            // Add deleted_at for Soft Deletes
            if (!Schema::hasColumn('categories', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            // Only drop timestamps if you are sure they were created by this migration.
            if (Schema::hasColumn('categories', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            if (Schema::hasColumn('categories', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
};
