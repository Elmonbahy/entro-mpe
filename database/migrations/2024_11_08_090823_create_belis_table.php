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
    Schema::create('belis', function (Blueprint $table) {
      $table->id();
      $table->string('nomor_faktur')->nullable()->unique();
      $table->string('nomor_pemesanan')->unique();
      $table->dateTime('tgl_faktur')->index();
      $table->dateTime('tgl_terima_faktur')->nullable()->index();
      $table->string('keterangan_faktur')->nullable();
      $table->enum('status_faktur', ['NEW', 'PROCESS_FAKTUR', 'PROCESS_GUDANG', 'DONE'])->default('NEW')->index();
      $table->foreignId('supplier_id')->constrained()->restrictOnDelete()->restrictOnUpdate();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('belis');
  }
};
