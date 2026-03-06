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
    Schema::create('barang_stock_awals', function (Blueprint $table) {
      $table->id();
      $table->foreignId('barang_id')->constrained()->restrictOnDelete();
      $table->unsignedInteger('jumlah_stock')->default(0);
      $table->dateTime('tgl_stock');
      $table->string('batch')->nullable();
      $table->dateTime('tgl_expired')->nullable();
      $table->enum('jenis_perubahan', ['AWAL', 'TAMBAH', 'KURANG']);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('barang_stock_awals');
  }
};
