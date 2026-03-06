<?php

namespace App\Http\Controllers\Fakturis\Laporan\Jual;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanJualExport implements FromView, ShouldAutoSize, WithEvents
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
    return view('pages.laporan-jual.fakturis.excel', [
      'data' => $this->data,
      'tglAwal' => $this->tglAwal,
      'tglAkhir' => $this->tglAkhir
    ]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $event->sheet->getStyle('A1:S1')->applyFromArray([
          'font' => [
            'bold' => true,
          ],
          'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'FFDDDDDD']
          ]
        ]);

        $event->sheet->getStyle('G2:G' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00');
        $event->sheet->getStyle('I2:I' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00');
        $event->sheet->getStyle('K2:S' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');
        $event->sheet->getStyle('F2:F' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');
      },
    ];
  }
}
