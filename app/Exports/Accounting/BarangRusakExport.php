<?php

namespace App\Exports\Accounting;

use App\Models\BarangRusak;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class BarangRusakExport implements FromQuery, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
  public function query(): Builder
  {
    return BarangRusak::query()
      ->join('barang_stocks', 'barang_rusaks.barang_stock_id', '=', 'barang_stocks.id')
      ->join('barangs', 'barang_stocks.barang_id', '=', 'barangs.id')
      ->select([
        'barang_rusaks.*',
        'barangs.nama as barang_nama',
        'barangs.satuan as satuan',
        'barang_stocks.batch as batch',
        'barang_stocks.tgl_expired as tgl_expired'
      ])
      ->orderBy('tgl_rusak', 'asc');
  }


  public function headings(): array
  {
    return [
      'Tanggal Input',
      'ID Barang',
      'Nama Barang',
      'Penyebab',
      'Tindakan',
      'Jumlah Rusak',
      'Satuan',
      'Batch',
      'Expired',
      'Keterangan',
    ];
  }

  public function map($barang_rusak): array
  {
    return [
      $barang_rusak->tgl_rusak
      ? Carbon::parse($barang_rusak->tgl_rusak)->format('d/m/Y')
      : '-',
      $barang_rusak->barang->id,
      $barang_rusak->barang->nama,
      $barang_rusak->penyebab->label(),
      $barang_rusak->tindakan->label(),
      $barang_rusak->jumlah_barang_rusak,
      $barang_rusak->barang->satuan,
      $barang_rusak->barangStock->batch ?? '-',
      $barang_rusak->barangStock->tgl_expired ?? $barang_rusak->barangStock->tgl_expired ? Carbon::parse($barang_rusak->barangStock->tgl_expired)->format('d/m/Y') : '-',
      $barang_rusak->keterangan ?? '-',
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
