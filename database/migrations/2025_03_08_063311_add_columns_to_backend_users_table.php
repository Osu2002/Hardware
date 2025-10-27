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
        Schema::table('backend_users', function (Blueprint $table) {
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('acc_number')->nullable();
            $table->string('payee_name')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('passbook_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backend_users', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'branch',
                'acc_number',
                'payee_name',
                'branch_code',
                'passbook_image'
            ]);
        });
    }
};
