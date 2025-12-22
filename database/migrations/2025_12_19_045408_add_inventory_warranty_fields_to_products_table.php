<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // tri-state: 1=in stock, 0=out of stock, null=not set
            $table->tinyInteger('in_stock')->nullable()->after('status');

            $table->unsignedInteger('stock_count')->nullable()->after('in_stock');

            // choose a unit (months is common). Keep it numeric.
            $table->unsignedInteger('warranty_period')->nullable()->after('stock_count');

            // free-typed text
            $table->string('warranty_type', 120)->nullable()->after('warranty_period');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['in_stock', 'stock_count', 'warranty_period', 'warranty_type']);
        });
    }
};
