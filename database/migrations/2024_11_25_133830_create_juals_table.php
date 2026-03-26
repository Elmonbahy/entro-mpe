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
    Schema::create('juals', function (Blueprint $table) {
      $table->id();
      $table->string('nomor_faktur')->unique()->comment('auto, e.g. format: 2024-01-01');
      $table->string('nomor_pemesanan')->nullable();
      $table->string('tipe_penjualan')->nullable();
      $table->dateTime('tgl_faktur')->index();
      $table->decimal('diskon_faktur', 5, 2)->default(0)->unsigned();
      $table->smallInteger('kredit')->default(0)->unsigned()->comment('lama kredit dalam hari');
      $table->decimal('ppn', 4, 2)->default(0)->unsigned();
      $table->string('keterangan_faktur')->nullable();

      $table->boolean('is_pungut_ppn')->default(1);
      $table->decimal('ongkir', 12, 2)->default(0)->unsigned();

      $table->json('bayar')->nullable()->comment('metode_bayar, tipe_bayar: nama bank or tunai, tgl_bayar, x_cicil: berapa kali dicicil, jika bayar lunas diawal x_cicil == 0, terbayar: berapa banyak yang dibayar, jika lunas diawal, nilai sesuai total_tagihan');
      $table->string('keterangan_bayar')->nullable();

      $table->enum('status_bayar', ['PAID', 'UNPAID'])->default('UNPAID')->index();
      $table->enum('status_faktur', ['NEW', 'PROCESS_FAKTUR', 'PROCESS_GUDANG', 'DONE'])->default('NEW')->index();
      $table->enum('status_kirim', ['PENDING', 'PARTIAL', 'SHIPPED'])
        ->default('PENDING')->index();

      $table->dateTime('cetak_titip_faktur_at')->nullable()->comment('Tgl cetak titip faktur.');
      // Relationships
      $table->foreignId('pelanggan_id')->constrained()->restrictOnDelete()->restrictOnUpdate();
      $table->foreignId('salesman_id')->nullable()->constrained()->restrictOnDelete()->restrictOnUpdate();

      // Index Gabungan untuk optimasi Query Laporan yang kompleks
      $table->index(['status_faktur', 'tgl_faktur'], 'idx_laporan_faktur_utama');
      $table->index(['salesman_id', 'tgl_faktur']);
      $table->index(['pelanggan_id', 'tgl_faktur']);

      // Timestamps
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('juals');
  }
};
