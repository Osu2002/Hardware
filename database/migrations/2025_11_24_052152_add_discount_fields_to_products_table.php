<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('discount_status')
                ->default(0)
                ->after('sale_price');

            $table->enum('discount_type', ['percent', 'amount'])
                ->nullable()
                ->after('discount_status');

            $table->decimal('discounted_amount', 10, 2)
                ->nullable()
                ->after('discount_type');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discount_status', 'discount_type', 'discounted_amount']);
        });
    }
};
