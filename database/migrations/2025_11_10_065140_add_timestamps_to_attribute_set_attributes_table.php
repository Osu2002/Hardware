<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attribute_set_attributes', function (Blueprint $table) {
            // add timestamps that Eloquent expects because of ->withTimestamps()
            $table->timestamp('created_at')->useCurrent()->after('sort_order');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('attribute_set_attributes', function (Blueprint $table) {
            $table->dropColumn(['created_at','updated_at']);
        });
    }
};
