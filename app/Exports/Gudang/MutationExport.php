<?php
namespace App\Exports\Gudang;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MutationExport implements FromCollection, WithHeadings, WithStyles
{
  protected $mutations;

  public function __construct($mutations)
  {
    $this->mutations = $mutations;
  }

  public function collection()
  {
    return collect($this->mutations);
  }

  public function headings(): array
  {
    return [
      'Barang ID',
      'Nama Barang',
      'Satuan',
      'Batch',
      'Total Harga Jual',
      'Total Harga Beli',
      'Tanggal Expired',
      'Stock Awal',
      'Stock Masuk',
      'Stock Keluar',
      'Retur Jual',
      'Retur Beli',
      'Stock Rusak',
      'Stock Akhir',
      'Sisa Stock'
    ];
  }

  public function styles(Worksheet $sheet)
  {
    return [
      // Style the first row (headings)
      1 => [
        'font' => ['bold' => true],
        'fill' => [
          'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
          'color' => ['argb' => 'FFDDDDDD']
        ]
      ]
    ];
  }
}
