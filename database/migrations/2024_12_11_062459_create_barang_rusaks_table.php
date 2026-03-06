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
    Schema::create('barang_rusaks', function (Blueprint $table) {
      $table->id();
      $table->dateTime('tgl_rusak');
      $table->enum('penyebab', ['RUSAK', 'EXPIRED']);
      $table->unsignedInteger('jumlah_barang_rusak');
      $table->enum('tindakan', ['DIMUSNAKAN', 'DIGANTI']);
      $table->string('keterangan')->nullable();

      $table->foreignId('barang_stock_id')->constrained()->restrictOnDelete();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('barang_rusaks');
  }
};
