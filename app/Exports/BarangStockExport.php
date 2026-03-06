<?php

namespace App\Exports;

use App\Models\BarangStock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BarangStockExport implements FromQuery, WithHeadings, WithMapping, WithEvents,ShouldAutoSize
{
  public function query(): Builder
  {
    return BarangStock::query()
      ->join('barangs', 'barang_stocks.barang_id', '=', 'barangs.id')
      ->leftJoin('brands', 'barangs.brand_id', '=', 'brands.id')
      ->select([
        'barangs.id as barang_id',
        'barangs.nama as barang_nama',
        'barangs.satuan as satuan',
        'brands.nama as brand_nama',
        \DB::raw('SUM(COALESCE(barang_stocks.jumlah_stock, 0)) as jumlah_stock')
      ])
      ->groupBy('barangs.id', 'barangs.nama', 'brands.nama', 'barangs.satuan')
      ->orderBy('brand_nama', 'asc')
      ->orderBy('barang_nama', 'asc');
  }

  public function headings(): array
  {
    return [
      "Brand",
      "Nama Barang",
      "Jumlah Stok",
      "Satuan"
    ];
  }

  public function map($barang): array
  {
    return [
      $barang->brand_nama ?? '-',
      $barang->barang_nama,
      $barang->jumlah_stock,
      $barang->satuan
    ];
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $event->sheet->getStyle('A1:D1')->applyFromArray([
          'font' => [
            'bold' => true,
          ],
          'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'FFDDDDDD']
          ]
        ]);

        // $event->sheet->getStyle('G2:G' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00');
        $event->sheet->getStyle('C2:C' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');
      },
    ];
  }
}
