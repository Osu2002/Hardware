<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // change these to nullable

            $table->string('seats', 255)->nullable()->change();
            $table->string('doors', 255)->nullable()->change();
            $table->string('passengers', 255)->nullable()->change();
            $table->string('interior_condition', 255)->nullable()->change();

        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // revert back to NOT NULL

        });
    }
};
