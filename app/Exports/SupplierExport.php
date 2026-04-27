<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SupplierExport implements FromCollection, WithHeadings
{
  public function collection()
  {
    return Supplier::select('id', 'nama', 'kota', 'alamat', 'contact_phone', 'contact_person')->get();
  }

  public function headings(): array
  {
    return [
      'Id',
      'Nama Supplier',
      'Kota',
      'Alamat',
      'Contact Phone',
      'Contact Person',
    ];
  }
}
