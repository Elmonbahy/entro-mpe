<?php

namespace App\Http\Controllers\Fakturis\Laporan\Beli;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanBeliExport implements FromView, ShouldAutoSize, WithEvents
{
  protected $data;
  protected $tglAwal;
  protected $tglAkhir;
  protected $filterBerdasarkan;

  public function __construct($data, $tglAwal, $tglAkhir, $filterBerdasarkan)
  {
    $this->data = $data;
    $this->tglAwal = $tglAwal;
    $this->tglAkhir = $tglAkhir;
    $this->filterBerdasarkan = $filterBerdasarkan;
  }

  public function view(): View
  {
    return view('pages.laporan-beli.fakturis.excel', [
      'data' => $this->data,
      'tglAwal' => $this->tglAwal,
      'tglAkhir' => $this->tglAkhir,
      'filterBerdasarkan' => $this->filterBerdasarkan,
    ]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        // Menyesuaikan header
        $event->sheet->getStyle('A1:S1')->applyFromArray([
          'font' => [
            'bold' => true,
          ],
          'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'FFDDDDDD']
          ]
        ]);
        $event->sheet->getStyle('B2:B' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $event->sheet->getStyle('J2:J' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');
        $event->sheet->getStyle('L2:L' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00');
        $event->sheet->getStyle('M2:M' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');
        $event->sheet->getStyle('N2:N' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00');
        $event->sheet->getStyle('O2:O' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');
        $event->sheet->getStyle('P2:P' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00');
        $event->sheet->getStyle('Q2:S' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');
      },
    ];
  }

}
