<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('product_attribute_values', function (Blueprint $t) {
      $t->id();
      $t->foreignId('product_id')->constrained('products')->cascadeOnDelete();
      $t->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();

      // typed values (use ONE per row depending on attribute.type)
      $t->text('value_text')->nullable();        // text/color/select (store raw text)
      $t->decimal('value_number', 18, 6)->nullable(); // number
      $t->boolean('value_boolean')->nullable();  // boolean

      $t->string('unit', 32)->nullable();        // optional override from attribute.unit
      $t->integer('sort_order')->default(0);

      $t->timestamps();
      $t->unique(['product_id','attribute_id']); // one value per attribute per product
    });
  }
  public function down(): void { Schema::dropIfExists('product_attribute_values'); }
};
