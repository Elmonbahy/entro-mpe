<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
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
      'kode' => 'SUPP' . str_pad(self::$counter, 3, '0', STR_PAD_LEFT),
      'nama' => $this->faker->company,
      'alamat' => $this->faker->address,
      'kota' => $this->faker->city,
      'npwp' => $this->faker->unique()->numerify('##.###.###.#-###.###'),
      'contact_person' => $this->faker->name,
      'contact_phone' => $this->faker->phoneNumber,
    ];
  }
}
