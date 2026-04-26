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
    Schema::create('juals', function (Blueprint $table) {
      $table->id();
      $table->string('nomor_faktur')->unique();
      $table->string('nomor_pemesanan')->nullable();
      $table->dateTime('tgl_faktur')->index();
      $table->string('keterangan_faktur')->nullable();
      $table->enum('status_faktur', ['NEW', 'PROCESS_FAKTUR', 'PROCESS_GUDANG', 'DONE'])->default('NEW')->index();
      // Relationships
      $table->foreignId('pelanggan_id')->constrained()->restrictOnDelete()->restrictOnUpdate();
      $table->foreignId('salesman_id')->nullable()->constrained()->restrictOnDelete()->restrictOnUpdate();

      // Index Gabungan untuk optimasi Query Laporan yang kompleks
      $table->index(['status_faktur', 'tgl_faktur'], 'idx_laporan_faktur_utama');
      $table->index(['salesman_id', 'tgl_faktur']);
      $table->index(['pelanggan_id', 'tgl_faktur']);

      // Timestamps
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('juals');
  }
};
