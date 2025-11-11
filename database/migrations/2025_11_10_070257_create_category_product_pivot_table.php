<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_product', function (Blueprint $t) {
            $t->engine = 'InnoDB';

            $t->id();

            // Use BIGINTs to align with Laravel's default id()
            $t->unsignedBigInteger('category_id');
            $t->unsignedBigInteger('product_id');

            // Fast lookups
            $t->index('category_id');
            $t->index('product_id');

            // Prevent duplicates
            $t->unique(['category_id','product_id']);

            $t->timestamps();

            // NOTE: no $t->foreign(...) lines on purpose
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
