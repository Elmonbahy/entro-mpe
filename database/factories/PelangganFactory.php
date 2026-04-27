<?php

namespace Database\Factories;

use App\Constants\Kota;
use App\Constants\TipePelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pelanggan>
 */
class PelangganFactory extends Factory
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
      'nama' => $this->faker->company,
      'alamat' => $this->faker->address,
      'kota' => $this->faker->randomElement(Kota::all()),
      'contact_person' => $this->faker->name,
      'contact_phone' => $this->faker->phoneNumber,
      'tipe' => $this->faker->randomElement(TipePelanggan::all()),
    ];
  }
}
