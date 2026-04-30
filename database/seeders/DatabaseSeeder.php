<?php

namespace Database\Seeders;

use App\Models\Beli;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {

    /**
     * NOTES:
     * We're not using this anymore
     * We use `MasterDataSeeder` to import real master data
     *
     */
    if (app()->environment('local')) {
      $this->call([
        BrandSeeder::class,
        SupplierSeeder::class,
        BarangSeeder::class,
        SalesmanSeeder::class,
        PelangganSeeder::class,
        BarangStockAwalSeeder::class,
        BeliSeeder::class,
        BeliDetailSeeder::class,
        JualSeeder::class,
        JualDetailSeeder::class,
        BarangStockSeeder::class,
        MutationSeeder::class

      ]);
    }

    $this->call([
      RoleSeeder::class,
      AdminSeeder::class, // don't forget to change password in production using php artisan tinker
    ]);
  }
}
