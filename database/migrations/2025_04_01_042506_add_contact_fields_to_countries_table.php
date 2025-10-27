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
        Schema::table('countries', function (Blueprint $table) {
            $table->string('phone1')->after('status');           
            $table->string('phone2')->nullable()->after('phone1'); 
            $table->string('location')->after('phone2');        
            $table->string('email')->nullable()->after('location'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['phone1', 'phone2', 'location', 'email']);
        });
    }
};