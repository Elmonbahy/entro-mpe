<?php

namespace Database\Seeders;
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
        GroupSeeder::class,
        BarangSeeder::class,
        SalesmanSeeder::class,
        PelangganSeeder::class,
        KendaraanSeeder::class
      ]);
    }

    $this->call([
      RoleSeeder::class,
      AdminSeeder::class, // don't forget to change password in production using php artisan tinker
    ]);
  }
}
