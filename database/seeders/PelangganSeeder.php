<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Pelanggan::factory()->count(10)->create();
  }
}
