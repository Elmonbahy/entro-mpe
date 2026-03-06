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
    Schema::table('belis', function (Blueprint $table) {
      $table->decimal('biaya_lainnya', 15, 2)->default(0)->unsigned()->after('materai');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('belis', function (Blueprint $table) {
      $table->dropColumn('biaya_lainnya');
    });
  }
};
