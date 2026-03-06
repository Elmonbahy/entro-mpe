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
    Schema::create('sample_out_details', function (Blueprint $table) {
      $table->id();
      $table->unsignedInteger('jumlah_barang_dipesan');
      $table->unsignedInteger('jumlah_barang_keluar')->default(0);
      $table->string('batch')->nullable();
      $table->dateTime('tgl_expired')->nullable();
      $table->enum('status_barang_keluar', ['BELUM_LENGKAP', 'LENGKAP'])->default('BELUM_LENGKAP');
      $table->foreignId('sample_out_id')->constrained()->cascadeOnDelete()->restrictOnUpdate();
      $table->foreignId('barang_id')->constrained()->restrictOnDelete()->restrictOnUpdate();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sample_out_details');
  }
};
