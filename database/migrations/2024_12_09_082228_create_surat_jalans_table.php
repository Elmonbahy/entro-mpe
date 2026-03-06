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
    Schema::create('surat_jalans', function (Blueprint $table) {
      $table->id();
      $table->dateTime('tgl_surat_jalan');
      $table->string('nomor_surat_jalan')->unique()->comment('auto, e.g. format: SJ/2024-01-1 (tahun, bulan, urutan, bulan & urutan direset setiap bulan baru)');
      $table->smallInteger('koli')->default(0)->unsigned();
      $table->string('staf_logistik')->nullable();
      $table->string('keterangan')->nullable();

      $table->foreignId('pelanggan_id')->constrained()->restrictOnDelete();
      $table->foreignId('kendaraan_id')->constrained()->restrictOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('surat_jalans');
  }
};
