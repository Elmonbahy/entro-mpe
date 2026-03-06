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
    Schema::create('barang_returs', function (Blueprint $table) {
      $table->id();
      $table->morphs('returnable');
      $table->unsignedInteger('jumlah_barang_retur');
      $table->string('keterangan')->nullable();
      $table->boolean('is_diganti')->default(false);
      $table->date('diganti_at')->nullable();
      $table->foreignId('barang_id')->constrained()->restrictOnDelete();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('barang_returs');
  }
};
