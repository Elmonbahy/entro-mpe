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
    Schema::create('sample_in_details', function (Blueprint $table) {
      $table->id();
      $table->unsignedInteger('jumlah_barang_dipesan');
      $table->unsignedInteger('jumlah_barang_masuk')->default(0);
      $table->string('batch')->nullable();
      $table->dateTime('tgl_expired')->nullable();
      $table->enum('status_barang_masuk', ['BELUM_LENGKAP', 'LENGKAP'])->default('BELUM_LENGKAP');
      $table->foreignId('sample_in_id')->constrained()->cascadeOnDelete()->restrictOnUpdate();
      $table->foreignId('barang_id')->constrained()->restrictOnDelete()->restrictOnUpdate();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sample_in_details');
  }
};
