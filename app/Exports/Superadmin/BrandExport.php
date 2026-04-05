<?php

namespace App\Exports\Superadmin;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BrandExport implements FromCollection, WithHeadings
{
  public function collection()
  {
    return Brand::select('id', 'nama')->get();
  }

  public function headings(): array
  {
    return [
      'Id',
      'Nama Brand',
    ];
  }
}
