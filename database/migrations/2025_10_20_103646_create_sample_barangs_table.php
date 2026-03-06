<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('sample_barangs', function (Blueprint $table) {
      $table->id();
      // Relasi ke barangs
      $table->foreignId('barang_id')
        ->unique() // satu barang hanya boleh punya satu sampel
        ->constrained('barangs')
        ->cascadeOnUpdate()
        ->restrictOnDelete();
      $table->string('satuan');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sample_barangs');
  }
};
