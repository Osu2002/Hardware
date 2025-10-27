<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
         DB::table('vehicles')
            ->whereNotIn('drive_type', ['Right Side Driving', 'Left Side Driving'])
            ->update(['drive_type' => 'Right Side Driving']);

        // Use raw SQL to change enum type
        DB::statement("ALTER TABLE vehicles MODIFY drive_type ENUM('Right Side Driving', 'Left Side Driving')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
          DB::statement("ALTER TABLE vehicles MODIFY drive_type ENUM('Old Value 1', 'Old Value 2')");
    }
};
