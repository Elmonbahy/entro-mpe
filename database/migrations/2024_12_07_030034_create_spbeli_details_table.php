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
        Schema::create('spbeli_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spbeli_id')->constrained()->cascadeOnDelete()->restrictOnUpdate();
            $table->foreignId('barang_id')->constrained()->restrictOnDelete()->restrictOnUpdate();
            $table->unsignedInteger('jumlah_barang_dipesan');
            $table->string('keterangan')->nullable();
            $table->decimal('harga_beli', 15, 2)->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('spbeli_details');
    }
};
