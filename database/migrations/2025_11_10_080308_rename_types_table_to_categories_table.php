<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename table only; keep the same columns
        if (Schema::hasTable('vehicle_type') && !Schema::hasTable('categories')) {
            Schema::rename('vehicle_type', 'categories');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('categories') && !Schema::hasTable('vehicle_type')) {
            Schema::rename('categories', 'vehicle_type');
        }
    }
};
