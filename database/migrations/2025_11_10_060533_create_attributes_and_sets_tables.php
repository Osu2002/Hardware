<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_attributes_and_sets_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('attributes', function (Blueprint $t) {
      $t->id();
      $t->string('code', 64)->unique();      // ex: colour, size
      $t->string('name', 120);               // ex: Colour, Size
      $t->enum('type', ['text','number','select','boolean','color'])->default('text');
      $t->string('unit', 32)->nullable();    // ex: mm, kg
      $t->boolean('is_filterable')->default(true);
      $t->boolean('is_variant_option')->default(false);
      $t->boolean('status')->default(1);
      $t->integer('sort_order')->default(0);
      $t->timestamps();
      $t->softDeletes();
    });

    Schema::create('attribute_options', function (Blueprint $t) {
      $t->id();
      $t->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
      $t->string('value', 120);              // ex: Red, Blue, 4L
      $t->string('hex', 16)->nullable();     // for color chips
      $t->integer('sort_order')->default(0);
      $t->timestamps();
    });

    Schema::create('attribute_sets', function (Blueprint $t) {
      $t->id();
      $t->string('name', 120);               // ex: Paint, Steel, Electrical
      $t->boolean('status')->default(1);
      $t->integer('sort_order')->default(0);
      $t->timestamps();
      $t->softDeletes();
    });

    Schema::create('attribute_set_attributes', function (Blueprint $t) {
      $t->id();
      $t->foreignId('attribute_set_id')->constrained('attribute_sets')->cascadeOnDelete();
      $t->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
      $t->boolean('is_required')->default(false);
      $t->integer('sort_order')->default(0);
      $t->unique(['attribute_set_id','attribute_id']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('attribute_set_attributes');
    Schema::dropIfExists('attribute_sets');
    Schema::dropIfExists('attribute_options');
    Schema::dropIfExists('attributes');
  }
};
