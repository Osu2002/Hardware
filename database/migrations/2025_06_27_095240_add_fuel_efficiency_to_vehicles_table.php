<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // decimal(8,2) lets you store values up to 999999.99
            $table->decimal('fuel_efficiency', 8, 2)
                  ->after('fuel_type')
                  ->comment('km per liter or Rs per km');
        });
    }

    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('fuel_efficiency');
        });
    }
};
