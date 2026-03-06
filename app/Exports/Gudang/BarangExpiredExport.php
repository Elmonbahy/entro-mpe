<?php

namespace App\Exports\Gudang;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BarangExpiredExport implements FromArray, WithHeadings, WithStyles, WithColumnFormatting, ShouldAutoSize
{
  protected $data;

  public function __construct($data)
  {
    $this->data = $data->toArray(); // ubah collection jadi array
  }

  public function array(): array
  {
    return array_map(function ($item) {
      return [
        $item['brand_nama'],
        $item['barang_nama'],
        $item['jumlah_stock'],
        $item['satuan'],
        $item['batch'],
        $item['tgl_expired'],
        $item['sisa_waktu'],
      ];
    }, $this->data);
  }

  public function headings(): array
  {
    return [
      'Brand',
      'Nama Barang',
      'Jumlah',
      'Satuan',
      'Batch',
      'Tanggal Expired',
      'Sisa Waktu',
    ];
  }

  public function styles(Worksheet $sheet)
  {
    return [
      1 => ['font' => ['bold' => true]],
    ];
  }

  public function columnFormats(): array
  {
    return [
      'E' => NumberFormat::FORMAT_TEXT,
      'C' => NumberFormat::FORMAT_NUMBER
    ];
  }
}
