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
      $table->string('kode', 10)->unique();
      $table->string('nama')->unique();
      $table->string('kota')->nullable();
      $table->string('alamat')->nullable();
      $table->string('npwp')->nullable();
      $table->string('contact_phone')->nullable();
      $table->string('contact_person')->nullable();
      $table->string('tipe')->comment('e.g. Rumah sakit, apotek, dinkes, etc')->nullable();
      $table->string('tipe_harga')->nullable()->comment('only SWASTA, PEMERINTAH');
      $table->string('area')->nullable();
      $table->decimal('plafon_hutang', 12, 2)->default(0)->unsigned();
      $table->smallInteger('limit_hari')->default(0)->unsigned()->comment('limit hari untuk plafon utang');
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
