<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
        $table->enum('monthly_price_currency', ['USD','LKR'])
            ->default('LKR')
            ->after('monthly_price');
        $table->enum('initial_payment_currency', ['USD','LKR'])
            ->default('LKR')
            ->after('initial_payment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            //
        });
    }
};
