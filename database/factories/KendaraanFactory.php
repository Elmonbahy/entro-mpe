<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class KendaraanFactory extends Factory
{
  private static $counter = 0;
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition() : array
  {
    self::$counter++;

    return [
      'nama' => $this->faker->company,
      'alamat' => $this->faker->address,
      'contact_person' => $this->faker->name,
      'contact_phone' => $this->faker->phoneNumber,
    ];
  }
}
