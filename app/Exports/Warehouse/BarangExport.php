<?php

namespace App\Exports\Warehouse;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangExport implements FromCollection, WithHeadings
{
  public function collection()
  {
    // Eager load relasi brand supaya bisa ambil nama brand
    return Barang::with('brand')
      ->get()
      ->map(function ($barang) {
        return [
          'id' => $barang->id,
          'brand' => $barang->brand ? $barang->brand->nama : '',
          'kode' => $barang->kode,
          'nama' => $barang->nama,
          'satuan' => $barang->satuan,
          'nie' => $barang->nie,
          'kegunaan' => $barang->kegunaan,
          'group' => $barang->group ? $barang->group->nama : '',
          'supplier' => $barang->supplier ? $barang->supplier->nama : '',
        ];
      });
  }

  public function headings(): array
  {
    return [
      'id',
      'Brand',
      'Kode',
      'Nama Barang',
      'Satuan',
      'NIE',
      'Kegunaan',
      'Group',
      'Supplier',
    ];
  }
}
