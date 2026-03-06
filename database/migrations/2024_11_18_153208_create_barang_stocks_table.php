<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('barang_stocks', function (Blueprint $table) {
      $table->id();
      $table->foreignId('barang_id')->constrained()->restrictOnDelete()->restrictOnUpdate();
      $table->unsignedInteger('jumlah_stock')->default(0);
      $table->string('batch')->nullable()->comment('dari beli_details');
      $table->dateTime('tgl_expired')->nullable()->comment('dari beli_details');
      $table->timestamps();

      $table->unique(['barang_id', 'batch', 'tgl_expired'], 'unique_barang_stock');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('barang_stocks');
  }
};
