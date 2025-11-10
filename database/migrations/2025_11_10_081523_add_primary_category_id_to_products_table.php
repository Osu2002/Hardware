<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'primary_category_id')) {
                $table->unsignedBigInteger('primary_category_id')
                      ->nullable()
                      ->after('attribute_set_id');
                $table->index('primary_category_id', 'products_primary_category_id_idx');
                // If you later want an FK, uncomment:
                // $table->foreign('primary_category_id')->references('id')->on('categories')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'primary_category_id')) {
                // $table->dropForeign(['primary_category_id']); // only if you added the FK
                $table->dropIndex('products_primary_category_id_idx');
                $table->dropColumn('primary_category_id');
            }
        });
    }
};
