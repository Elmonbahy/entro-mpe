<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $data = [
      [
        'name' => 'Admin Super',
        'slug' => 'su',
      ],
      [
        'name' => 'Admin Fakturis',
        'slug' => 'af'
      ],
      [
        'name' => 'Admin Gudang',
        'slug' => 'ag'
      ],
      [
        'name' => 'Admin Supervisor',
        'slug' => 'as'
      ],
    ];

    // Bulk insert
    Role::insert($data);
  }
}
