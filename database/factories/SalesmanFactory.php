<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Salesman>
 */
class SalesmanFactory extends Factory
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
      'kode' => 'S' . str_pad(self::$counter, 3, '0', STR_PAD_LEFT),
      'nama' => $this->faker->name()
    ];
  }
}
