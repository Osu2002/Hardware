<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    
    public function up(): void
    {
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN transmission ENUM('Auto', 'Manual', 'Triptonic') NOT NULL");
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN fuel_type ENUM('Diesel', 'Petrol', 'Hybrid', 'Electric') NOT NULL");
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN availability ENUM('Available', 'Arriving', 'Sold') NOT NULL");
    }

  
    public function down(): void
    {
       
    }
};
