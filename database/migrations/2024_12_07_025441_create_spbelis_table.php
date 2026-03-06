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
        Schema::create('spbelis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pemesanan')->unique()->comment('auto, e.g. format: SP/APM/2024-01');
            $table->dateTime('tgl_sp')->comment('tanggal surat pesanan');
            $table->string('keterangan')->nullable();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete()->restrictOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('spbelis');
    }
};
