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
      $table->string('nomor_pemesanan')->unique()->comment('auto, e.g. format: SP/APM/2024-01');
      $table->dateTime('tgl_faktur')->index();
      $table->dateTime('tgl_terima_faktur')->nullable()->index();
      $table->decimal('diskon_faktur', 5, 2)->default(0)->unsigned();
      $table->smallInteger('kredit')->default(0)->unsigned()->comment('lama kredit dalam hari');
      $table->decimal('ppn', 4, 2)->default(0)->unsigned();
      $table->decimal('ongkir', 15, 2)->default(0)->unsigned();
      $table->decimal('materai', 15, 2)->default(0)->unsigned();
      $table->decimal('biaya_lainnya', 15, 2)->default(0)->unsigned();
      $table->string('keterangan_faktur')->nullable();

      // Bayar
      $table->json('bayar')->nullable()->comment('metode_bayar, tipe_bayar: nama bank or tunai, tgl_bayar, x_cicil: berapa kalai dicicil, jika bayar lunas diawal x_cicil == 0, terbayar: berapa banyak yang dibayar, jika lunas diawal, nilai sesuai total_tagihan');

      $table->enum('status_bayar', ['PAID', 'UNPAID'])->default('UNPAID')->index();
      $table->enum('status_faktur', ['NEW', 'PROCESS_FAKTUR', 'PROCESS_GUDANG', 'DONE'])->default('NEW')->index();

      $table->string('keterangan_bayar')->nullable();

      $table->foreignId('supplier_id')->constrained()->restrictOnDelete()->restrictOnUpdate();

      // Index Gabungan untuk optimasi Query Laporan yang kompleks
      $table->index(['status_faktur', 'tgl_faktur'], 'idx_beli_status_tgl');
      $table->index(['status_faktur', 'tgl_terima_faktur'], 'idx_beli_status_terima');
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
