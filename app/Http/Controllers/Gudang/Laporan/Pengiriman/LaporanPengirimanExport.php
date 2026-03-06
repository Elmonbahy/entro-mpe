<?php

namespace App\Http\Controllers\Gudang\Laporan\Pengiriman;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanPengirimanExport implements FromView, ShouldAutoSize, WithEvents
{
  protected $data;
  protected $tglAwal;
  protected $tglAkhir;

  public function __construct($data, $tglAwal, $tglAkhir)
  {
    $this->data = $data;
    $this->tglAwal = $tglAwal;
    $this->tglAkhir = $tglAkhir;
  }

  public function view(): View
  {
    return view('pages.laporan-pengiriman.gudang.excel', [
      'data' => $this->data,
      'tglAwal' => $this->tglAwal,
      'tglAkhir' => $this->tglAkhir
    ]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        // Menyesuaikan header
        $event->sheet->getStyle('A1:O1')->applyFromArray([
          'font' => [
            'bold' => true,
          ],
          'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'FFDDDDDD']
          ]
        ]);
        $event->sheet->getStyle('A2:A' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $event->sheet->getStyle('M2:N' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');
      },
    ];
  }

}
