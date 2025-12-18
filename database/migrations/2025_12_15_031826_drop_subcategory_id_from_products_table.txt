<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // drop FK first (Laravel default naming)
            $table->dropForeign(['subcategory_id']); 
            // then drop column
            $table->dropColumn('subcategory_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('subcategory_id')->nullable();

            // re-add FK (change table/column names if yours differ)
            $table->foreign('subcategory_id')
                  ->references('id')
                  ->on('subcategories')
                  ->nullOnDelete();
        });
    }
};