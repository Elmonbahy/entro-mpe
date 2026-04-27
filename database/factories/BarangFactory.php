<?php

namespace Database\Factories;

use App\Constants\Satuan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barang>
 */
class BarangFactory extends Factory
{
  private static $counter = 0;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    self::$counter++;

    return [
      'kode' => $this->faker->regexify('[A-Z]{3}[0-9]{4}'),
      'nama' => $this->faker->company,
      'satuan' => $this->faker->randomElement(Satuan::all()),
      'nie' => $this->faker->numberBetween(1, 100),
      'group_id' => \App\Models\Group::factory(),
      'brand_id' => \App\Models\Brand::factory(),
      'supplier_id' => \App\Models\Supplier::factory(),
    ];
  }
}
