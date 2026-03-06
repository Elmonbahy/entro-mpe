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
    Schema::create('sample_ins', function (Blueprint $table) {
      $table->id();
      $table->string('nomor_sample')->nullable()->unique();
      $table->dateTime('tanggal');
      $table->string('keterangan')->nullable();

      $table->enum('status_sample', ['NEW', 'PROCESS_SAMPLE', 'PROCESS_GUDANG', 'DONE'])->default('NEW');
      $table->foreignId('supplier_id')->constrained()->restrictOnDelete()->restrictOnUpdate();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sample_ins');
  }
};
