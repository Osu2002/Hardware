<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCountriesTableMakeCodeNullableAndAddSlug extends Migration
{
   
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('code')->nullable()->change();

            $table->string('slug')->unique();
        });
    }

   
    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');

            $table->string('code')->nullable(false)->change();
        });
    }
}
