<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('products', function (Blueprint $t) {
      $t->id();
      $t->string('sku', 64)->unique();
      $t->string('slug', 160)->unique();
      $t->string('name', 160);
      $t->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
      // category: if single category per product, keep category_id; if many-to-many, use pivot only.
$t->unsignedBigInteger('category_id')->nullable()->index();
      $t->foreignId('uom_id')->nullable()->constrained('uoms')->nullOnDelete();
      $t->foreignId('attribute_set_id')->nullable()->constrained('attribute_sets')->nullOnDelete();

      // catalog flags
      $t->boolean('status')->default(1);       // Active/Inactive
      $t->boolean('is_featured')->default(0);
      $t->integer('sort_order')->default(0);

      // pricing & stock (basic)
      $t->decimal('price', 12, 2)->default(0);
      $t->decimal('sale_price', 12, 2)->nullable();
      $t->date('sale_from')->nullable();
      $t->date('sale_to')->nullable();

      $t->integer('qty')->default(0);
      $t->boolean('manage_stock')->default(true);
      $t->boolean('is_visible')->default(true);

      // SEO
      $t->string('meta_title', 180)->nullable();
      $t->string('meta_keywords', 255)->nullable();
      $t->text('meta_description')->nullable();

      // content
      $t->text('short_description')->nullable();
      $t->longText('description')->nullable();

      $t->timestamps();
      $t->softDeletes();
    });
  }
  public function down(): void { Schema::dropIfExists('products'); }
};
