<?php

namespace App\Livewire\Dashboard;

use App\Models\Beli;
use Carbon\Carbon;
use Livewire\Component;

class MonthlyReceiveTable extends Component
{
  public $selectedYear;
  public $years = [];
  public $monthlyData = [];

  public function mount()
  {
    $this->selectedYear = (int) now()->year;

    // Ambil semua tahun dari data Beli
    $this->years = Beli::selectRaw('YEAR(tgl_faktur) as year')
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
      'done' => 0,
      'process_gudang' => 0,
      'process_faktur' => 0,
      'new' => 0,
    ]);

    $data = Beli::selectRaw('
        MONTH(tgl_faktur) as month,
        COUNT(*) as jumlah_faktur,
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
        'done' => $item->done,
        'process_gudang' => $item->process_gudang,
        'process_faktur' => $item->process_faktur,
        'new' => $item->new,
      ];
    }

    $this->monthlyData = [];
    for ($month = 1; $month <= 12; $month++) {
      $this->monthlyData[] = [
        'month' => Carbon::create(null, $month, 1)->format('F'), // aman di server
        'jumlah_faktur' => $monthlyData[$month]['jumlah_faktur'],
        'done' => $monthlyData[$month]['done'],
        'process_gudang' => $monthlyData[$month]['process_gudang'],
        'process_faktur' => $monthlyData[$month]['process_faktur'],
        'new' => $monthlyData[$month]['new'],
      ];
    }
  }

  public function render()
  {
    return view('livewire.dashboard.monthly-receive-table');
  }
}
