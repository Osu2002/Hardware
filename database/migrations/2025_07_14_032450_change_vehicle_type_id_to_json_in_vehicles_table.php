<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeVehicleTypeIdToJsonInVehiclesTable extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // if you want to keep the same name:
            $table->json('vehicle_type_id')
                  ->nullable()
                  ->default('[]')
                  ->change();
            
      
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
          
            $table->unsignedBigInteger('vehicle_type_id')
                  ->change();
        });
    }
}
