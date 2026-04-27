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
    Schema::create('pelanggans', function (Blueprint $table) {
      $table->id();
      $table->string('nama')->unique();
      $table->string('kota')->nullable();
      $table->string('alamat')->nullable();
      $table->string('contact_phone')->nullable();
      $table->string('contact_person')->nullable();
      $table->string('tipe')->comment('e.g. Rumah sakit, apotek, dinkes, etc')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pelanggans');
  }
};
