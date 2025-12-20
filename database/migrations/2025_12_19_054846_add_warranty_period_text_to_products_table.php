<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'warranty_period')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('warranty_period', 120)->nullable()->after('stock_count');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'warranty_period')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('warranty_period');
            });
        }
    }
};
