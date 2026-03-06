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
        Schema::create('spjuals', function (Blueprint $table) {
            $table->id();

            $table->string('nomor_pemesanan')->unique();
            $table->dateTime('tgl_sp');
            $table->string('keterangan')->nullable();
            $table->foreignId('pelanggan_id')->constrained()->restrictOnDelete()->restrictOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('spjuals');
    }
};
