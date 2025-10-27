<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // change these to nullable
            $table->unsignedBigInteger('vehicle_type_id')->nullable()->change();
            $table->unsignedBigInteger('manufacture_id')->nullable()->change();
            $table->unsignedBigInteger('vehicle_model_id')->nullable()->change();
            $table->string('seo_url', 255)->nullable()->change();
            $table->string('ex_color', 255)->nullable()->change();
            $table->string('in_color', 255)->nullable()->change();
            $table->string('features', 255)->nullable()->change();
            $table->string('transmission', 255)->nullable()->change();
            $table->string('engine_capacity', 255)->nullable()->change();
            $table->string('fuel_type', 255)->nullable()->change();
            $table->decimal('fuel_efficiency', 8, 2)->nullable()->change();
            $table->string('drive_type', 255)->nullable()->change();
            $table->string('availability', 255)->nullable()->change();
            $table->string('vehicle_status', 255)->nullable()->change();
            $table->string('monthly_price_currency', 255)->nullable()->change();
            $table->string('initial_payment_currency', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // revert back to NOT NULL
            $table->unsignedBigInteger('vehicle_type_id')->nullable(false)->change();
            $table->unsignedBigInteger('manufacture_id')->nullable(false)->change();
            $table->unsignedBigInteger('vehicle_model_id')->nullable(false)->change();
            $table->string('seo_url', 255)->nullable(false)->change();
            $table->string('ex_color', 255)->nullable(false)->change();
            $table->string('in_color', 255)->nullable(false)->change();
            $table->string('features', 255)->nullable(false)->change();
            $table->enum('transmission', ['Auto','Manual','Triptonic'])->nullable(false)->change();
            $table->string('engine_capacity', 255)->nullable(false)->change();
            $table->enum('fuel_type', ['Diesel','Petrol','Hybrid','Electric'])->nullable(false)->change();
            $table->decimal('fuel_efficiency', 8, 2)->nullable(false)->change();
            $table->enum('drive_type', ['Right Side Driving','Left Side Driving'])->nullable(false)->change();
            $table->enum('availability', ['Available','Arriving','Sold'])->nullable(false)->change();
            $table->enum('vehicle_status', ['new','used'])->nullable(false)->change();
            $table->enum('monthly_price_currency', ['USD','LKR'])->nullable(false)->change();
            $table->enum('initial_payment_currency', ['USD','LKR'])->nullable(false)->change();
        });
    }
};
