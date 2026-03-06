<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up() : void
  {
    Schema::create('surat_jalan_details', function (Blueprint $table) {
      $table->id();
      $table->unsignedInteger('jumlah_barang_dikirim')->default(0);

      $table->foreignId('surat_jalan_id')->constrained()->cascadeOnDelete();
      $table->foreignId('jual_detail_id')->constrained()->cascadeOnDelete();
      $table->timestamps();

      $table->unique(['surat_jalan_id', 'jual_detail_id'], 'unique_sj_detail');

    });
  }

  /**
   * Reverse the migrations.
   */
  public function down() : void
  {
    Schema::dropIfExists('surat_jalan_details');
  }
};
