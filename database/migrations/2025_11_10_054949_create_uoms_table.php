<?php
// database/migrations/XXXX_XX_XX_XXXXXX_create_uoms_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('uoms', function (Blueprint $t) {
      $t->id();
      $t->string('code', 32)->unique();      // pcs, kg, m, L
      $t->string('name', 80);                // Pieces, Kilogram, Meter
      $t->boolean('status')->default(1);     // 1=Active, 0=Inactive (kept name "status" to match your UI)
      $t->integer('sort_order')->default(0);
      $t->timestamps();
      $t->softDeletes();
    });
  }
  public function down(): void {
    Schema::dropIfExists('uoms');
  }
};
