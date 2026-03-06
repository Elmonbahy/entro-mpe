<?php

namespace App\Exports\Accounting;

use App\Models\BarangStockAwal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class BarangStockAwalExport implements FromQuery, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
  public function query(): Builder
  {
    return BarangStockAwal::query()
      ->join('barangs', 'barang_stock_awals.barang_id', '=', 'barangs.id')
      ->leftJoin('brands', 'barangs.brand_id', '=', 'brands.id')
      ->select([
        'barang_stock_awals.*',
        'barangs.nama as barang_nama',
        'barangs.satuan as barang_satuan',
        'barangs.id as barang_id',
        'brands.nama as brand_nama'
      ]);
  }


  public function headings(): array
  {
    return [
      'Tanggal Penyesuaian',
      'Perubahan',
      'Brand',
      'ID Barang',
      'Nama Barang',
      'Batch',
      'Expired',
      'Jumlah stock',
      'Satuan',
      'Keterangan',
    ];
  }

  public function map($barang_stock_awal): array
  {
    return [
      $barang_stock_awal->tgl_stock
      ? Carbon::parse($barang_stock_awal->tgl_stock)->format('d/m/Y')
      : '-',
      $barang_stock_awal->jenis_perubahan->label(),
      $barang_stock_awal->barang->brand->nama,
      $barang_stock_awal->barang->id,
      $barang_stock_awal->barang->nama,
      $barang_stock_awal->batch ?? '-',
      $barang_stock_awal->tgl_stock
      ? Carbon::parse($barang_stock_awal->tgl_expired)->format('d/m/Y')
      : '-',
      $barang_stock_awal->jumlah_stock,
      $barang_stock_awal->barang->satuan,
      $barang_stock_awal->keterangan ?? '-',
    ];
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $event->sheet->getStyle('A1:J1')->applyFromArray([
          'font' => [
            'bold' => true,
          ],
          'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'FFDDDDDD']
          ]
        ]);
      },
    ];
  }
}
