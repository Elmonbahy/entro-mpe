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
    Schema::table('surat_jalans', function (Blueprint $table) {
      $table->dateTime('tgl_kembali_surat_jalan')->nullable()->after('tgl_surat_jalan');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('surat_jalans', function (Blueprint $table) {
      $table->dropColumn('tgl_kembali_surat_jalan');
    });
  }
};
