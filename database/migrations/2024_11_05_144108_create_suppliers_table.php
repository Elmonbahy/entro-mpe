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
    Schema::create('suppliers', function (Blueprint $table) {
      $table->id();
      $table->string('kode', 10)->unique();
      $table->string('nama')->unique();
      $table->string('alamat')->nullable();
      $table->string('kota')->nullable();
      $table->string('npwp')->nullable();
      $table->string('contact_person')->nullable();
      $table->string('contact_phone')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('suppliers');
  }
};
