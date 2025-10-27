<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Drop old 'condition' if it exists
            if (Schema::hasColumn('vehicles', 'condition')) {
                $table->dropColumn('condition');
            }
            // Add new ENUM column with default “used”
            if (! Schema::hasColumn('vehicles', 'vehicle_status')) {
                $table->enum('vehicle_status', ['new','used'])
                      ->default('used')
                      ->after('availability');
            }
        });
    }

    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('vehicle_status');
        });
    }
};
