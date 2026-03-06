<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // get super user
    $superUser = Role::where('slug', 'su')->first();
    $fakturUser = Role::where('slug', 'af')->first();
    $gudangUser = Role::where('slug', 'ag')->first();
    $keuanganUser = Role::where('slug', 'ak')->first();
    $logistikUser = Role::where('slug', 'al')->first();
    // TODO: minta daftar user

    User::create([
      'name' => 'John Doe',
      'username' => 'superadmin',
      'email' => 'superadmin@apm.com',
      'password' => Hash::make('password'),
      'role_id' => $superUser->id
    ]);

    User::create([
      'name' => 'Emma Faktur',
      'username' => 'fakturis',
      'email' => 'fakturis@apm.com',
      'password' => Hash::make('password'),
      'role_id' => $fakturUser->id
    ]);

    User::create([
      'name' => 'Emma Gudang',
      'username' => 'gudang',
      'email' => 'gudang@apm.com',
      'password' => Hash::make('password'),
      'role_id' => $gudangUser->id
    ]);

    User::create([
      'name' => 'Emma keuangan',
      'username' => 'keuangan',
      'email' => 'keuangan@apm.com',
      'password' => Hash::make('password'),
      'role_id' => $keuanganUser->id
    ]);

    User::create([
      'name' => 'Emma Logistik',
      'username' => 'logistik',
      'email' => 'logistik@apm.com',
      'password' => Hash::make('password'),
      'role_id' => $logistikUser->id
    ]);
  }
}
