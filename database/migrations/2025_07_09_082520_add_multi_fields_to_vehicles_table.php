<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // add JSON columns for your new multiple‐entry fields
            $table->json('engine_capacities')->nullable();
            $table->json('fuel_types')->nullable();
            $table->json('grades')->nullable();
            $table->json('chassis_nos')->nullable();

            // drop the old single‐value columns (if you don’t need them any more)
            $table->dropColumn(['engine_capacity','fuel_type','auction_grade','chassis_no']);
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // restore the old single‐value columns
            $table->string('engine_capacity')->nullable();
            $table->enum('fuel_type', ['Diesel','Petrol','Hybrid','Electric'])->nullable();
            $table->string('auction_grade')->nullable();
            $table->string('chassis_no')->nullable();

            // drop the JSON columns
            $table->dropColumn(['engine_capacities','fuel_types','grades','chassis_nos']);
        });
    }
};
