<?php

namespace App\Livewire\Dashboard;

use App\Models\Jual;
use Carbon\Carbon;
use Livewire\Component;

class MonthlyShipTable extends Component
{
  public $selectedYear;
  public $years = [];
  public $monthlyData = [];

  public function mount()
  {
    $this->selectedYear = (int) now()->year;

    // Ambil tahun langsung dari DB (tanpa Carbon::parse)
    $this->years = Jual::selectRaw('YEAR(tgl_faktur) as year')
      ->groupBy('year')
      ->orderBy('year', 'desc')
      ->pluck('year')
      ->toArray();

    $this->loadData();
  }

  public function updatedSelectedYear()
  {
    $this->loadData();
  }

  private function loadData()
  {
    $monthlyData = array_fill(1, 12, [
      'jumlah_faktur' => 0,
      'shipped' => 0,
      'partial' => 0,
      'pending' => 0,
      'done' => 0,
      'process_gudang' => 0,
      'process_faktur' => 0,
      'new' => 0,
    ]);

    $data = Jual::selectRaw('
        MONTH(tgl_faktur) as month,
        COUNT(*) as jumlah_faktur,
        SUM(CASE WHEN status_kirim="SHIPPED" THEN 1 ELSE 0 END) as shipped,
        SUM(CASE WHEN status_kirim="PARTIAL" THEN 1 ELSE 0 END) as partial,
        SUM(CASE WHEN status_kirim="PENDING" THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status_faktur="DONE" THEN 1 ELSE 0 END) as done,
        SUM(CASE WHEN status_faktur="PROCESS_GUDANG" THEN 1 ELSE 0 END) as process_gudang,
        SUM(CASE WHEN status_faktur="PROCESS_FAKTUR" THEN 1 ELSE 0 END) as process_faktur,
        SUM(CASE WHEN status_faktur="NEW" THEN 1 ELSE 0 END) as new
      ')
      ->whereYear('tgl_faktur', $this->selectedYear)
      ->groupBy('month')
      ->orderBy('month')
      ->get();

    foreach ($data as $item) {
      $monthlyData[$item->month] = [
        'jumlah_faktur' => $item->jumlah_faktur,
        'shipped' => $item->shipped,
        'partial' => $item->partial,
        'pending' => $item->pending,
        'done' => $item->done,
        'process_gudang' => $item->process_gudang,
        'process_faktur' => $item->process_faktur,
        'new' => $item->new,
      ];
    }

    $this->monthlyData = [];
    for ($month = 1; $month <= 12; $month++) {
      $this->monthlyData[] = [
        'month' => Carbon::create(null, $month, 1)->format('F'), // English safer
        'jumlah_faktur' => $monthlyData[$month]['jumlah_faktur'],
        'shipped' => $monthlyData[$month]['shipped'],
        'partial' => $monthlyData[$month]['partial'],
        'pending' => $monthlyData[$month]['pending'],
        'done' => $monthlyData[$month]['done'],
        'process_gudang' => $monthlyData[$month]['process_gudang'],
        'process_faktur' => $monthlyData[$month]['process_faktur'],
        'new' => $monthlyData[$month]['new'],
      ];
    }
  }

  public function render()
  {
    return view('livewire.dashboard.monthly-ship-table');
  }
}
