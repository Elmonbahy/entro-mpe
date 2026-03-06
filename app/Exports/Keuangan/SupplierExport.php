<?php

namespace App\Exports\Keuangan;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SupplierExport implements FromCollection, WithHeadings
{
  public function collection()
  {
    return Supplier::select('id', 'kode', 'nama', 'kota', 'alamat', 'npwp', 'contact_phone', 'contact_person')->get();
  }

  public function headings(): array
  {
    return [
      'Id',
      'Kode',
      'Nama Supplier',
      'Kota',
      'Alamat',
      'NPWP',
      'Contact Phone',
      'Contact Person',
    ];
  }
}
