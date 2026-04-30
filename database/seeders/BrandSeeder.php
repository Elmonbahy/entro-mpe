<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BrandSeeder extends Seeder
{
  public function run(): void
  {
    $file = database_path('data/brands.csv');
    if (!File::exists($file))
      return;

    $csvData = file($file);
    // Membersihkan karakter aneh (BOM) dan spasi di header
    $rawHeader = array_shift($csvData);
    $cleanHeader = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $rawHeader);
    $headers = array_map('trim', str_getcsv($cleanHeader, ";"));

    foreach ($csvData as $row) {
      $data = str_getcsv($row, ";");
      if (count($headers) !== count($data))
        continue;
      $item = array_combine($headers, $data);
      $item = array_map(fn($v) => (trim($v) === '' || strtoupper(trim($v)) === 'NULL') ? null : trim($v), $item);
      Brand::create($item);
    }
  }
}