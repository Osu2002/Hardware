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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->text('message')->nullable();
            $table->string('purchase_time');
            $table->string('payment_method');
            $table->string('url');
            $table->string('vehicle_id');
            $table->string('dealer_id');
            $table->string('customer_id')->nullable();
            $table->enum('type',['local', 'auction']);
            $table->enum('status', ['pending','contacted', 'negotiating', 'advance_paid','awaiting_for_shipping','cancelled']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
