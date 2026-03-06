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
    Schema::create('barangs', function (Blueprint $table) {
      $table->id();
      $table->foreignId('brand_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();
      $table->string('kode')->unique()->nullable();
      $table->string('nama');
      $table->string('satuan');
      $table->string('nie')->nullable();
      $table->decimal('harga_jual_pemerintah', 12, 2)->unsigned()->default(0)->nullable(); // Menggunakan decimal dan unsigned
      $table->decimal('harga_jual_swasta', 12, 2)->unsigned()->default(0)->nullable(); // Menggunakan decimal dan unsigned
      $table->decimal('harga_beli', 12, 2)->unsigned()->default(0)->nullable(); // Menggunakan decimal dan unsigned
      $table->text('kegunaan')->nullable();
      $table->timestamps();

      $table->unique(['nama', 'brand_id'], 'unique_barang_name');

      // assign foreign key
      $table->foreignId('group_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();
      $table->foreignId('supplier_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('barangs');
  }
};
