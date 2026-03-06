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
    Schema::create('mutations', function (Blueprint $table) {
      $table->id();
      $table->morphs('mutationable');
      $table->dateTime('tgl_mutation')->nullable();
      $table->foreignId('barang_id')->constrained()->restrictOnDelete();
      $table->string('batch')->nullable();
      $table->dateTime('tgl_expired')->nullable();

      $table->unsignedInteger('stock_awal')->default(0);
      $table->unsignedInteger('stock_masuk')->default(0);
      $table->unsignedInteger('stock_keluar')->default(0);
      $table->unsignedInteger('stock_retur_jual')->default(0);
      $table->unsignedInteger('stock_retur_beli')->default(0);
      $table->unsignedInteger('stock_rusak')->default(0);
      $table->unsignedInteger('stock_akhir')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('mutations');
  }
};
