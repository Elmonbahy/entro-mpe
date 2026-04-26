<?php

namespace App\Http\Controllers\Fakturis\Laporan\FakturJual;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanFakturJualExport implements FromView, ShouldAutoSize, WithEvents
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
    return view('pages.laporan-jual-faktur.fakturis.excel', [
      'data' => $this->data,
      'tglAwal' => $this->tglAwal,
      'tglAkhir' => $this->tglAkhir
    ]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $event->sheet->getStyle('A1:I1')->applyFromArray([
          'font' => [
            'bold' => true,
          ],
          'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'FFDDDDDD']
          ]
        ]);


        $event->sheet->getStyle('I2:I' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0.00');
      },
    ];
  }
}
