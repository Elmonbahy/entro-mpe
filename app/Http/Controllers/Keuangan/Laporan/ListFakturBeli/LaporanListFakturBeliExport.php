<?php

namespace App\Http\Controllers\Keuangan\Laporan\ListFakturBeli;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanListFakturBeliExport implements FromView, ShouldAutoSize, WithEvents
{
  protected $data;
  protected $tglAwal;
  protected $tglAkhir;
  protected $filterBerdasarkan;

  public function __construct($data, $tglAwal, $tglAkhir, $filterBerdasarkan = 'tgl_faktur')
  {
    $this->data = $data;
    $this->tglAwal = $tglAwal;
    $this->tglAkhir = $tglAkhir;
    $this->filterBerdasarkan = $filterBerdasarkan;
  }

  public function view(): View
  {
    return view('pages.laporan-list-faktur-beli.keuangan.excel', [
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
        $event->sheet->getStyle('A1:O1')->applyFromArray([
          'font' => [
            'bold' => true,
          ],
          'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'FFDDDDDD']
          ]
        ]);
        $event->sheet->getStyle('B2:B' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $event->sheet->getStyle('J2:J' . $event->sheet->getHighestRow())
          ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $event->sheet->getStyle('K2:O' . $event->sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0');
      },
    ];
  }

}
