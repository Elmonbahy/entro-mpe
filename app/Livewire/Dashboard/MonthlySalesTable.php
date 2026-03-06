<?php

namespace App\Livewire\Dashboard;

use App\Models\Jual;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class MonthlySalesTable extends Component
{
  public $selectedYear;
  public $years = [];
  public $monthlyData = [];
  public $grandTotal = 0;
  public $lastUpdated;

  public function mount()
  {
    $this->selectedYear = now()->year;

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
    $cacheKey = "monthly_sales_{$this->selectedYear}";

    // Cek cache
    $cached = Cache::get($cacheKey);
    if ($cached) {
      $this->monthlyData = $cached['monthly'] ?? [];
      $this->grandTotal = $cached['grand'] ?? [];
      $this->lastUpdated = $cached['updated_at'] ?? now()->format('d-m-Y H:i:s');

      return;
    }

    // Hitung ulang dari database
    $query = Jual::with('jualDetails')
      ->select('id', 'tgl_faktur', 'is_pungut_ppn', 'bayar')
      ->whereYear('tgl_faktur', $this->selectedYear)
      ->orderBy('tgl_faktur');

    $data = $query->get()
      ->groupBy(fn($item) => Carbon::parse($item->tgl_faktur)->month)
      ->map(function ($group, $month) {
        $monthName = Carbon::create()->month($month)->translatedFormat('F');

        $total_faktur = $group->sum(fn($jual) => $jual->total_faktur);
        $total_lunas = $group->filter(fn($jual) => $jual->bayar >= $jual->total_faktur)
          ->sum(fn($jual) => $jual->total_faktur);
        $total_belum_lunas = $total_faktur - $total_lunas;

        $persentase_lunas = $total_faktur > 0
          ? round(($total_lunas / $total_faktur) * 100, 2)
          : 0;

        return [
          'month_name' => $monthName,
          'total_faktur' => (int) $total_faktur,
          'total_lunas' => (int) $total_lunas,
          'total_belum_lunas' => (int) $total_belum_lunas,
          'persentase_lunas' => $persentase_lunas,
        ];
      })
      ->values()
      ->toArray();

    $grandTotal = [
      'total_faktur' => array_sum(array_column($data, 'total_faktur')),
      'total_lunas' => array_sum(array_column($data, 'total_lunas')),
      'total_belum_lunas' => array_sum(array_column($data, 'total_belum_lunas')),
      'persentase_lunas' => 0
    ];

    // Simpan ke Cache — include timestamp
    $cachedData = [
      'monthly' => $data,
      'grand' => $grandTotal,
      'updated_at' => now()->format('d-m-Y H:i:s')
    ];

    //refresh otomatis setiap jam 00:00 (bukan 24 jam), bisa ganti:
    Cache::put($cacheKey, $cachedData, now()->endOfDay());

    $this->monthlyData = $data;
    $this->grandTotal = $grandTotal;
    $this->lastUpdated = $cachedData['updated_at'];
  }
  public function refreshData()
  {
    Cache::forget("monthly_sales_{$this->selectedYear}");
    $this->loadData();
  }

  public function render()
  {
    return view('livewire.dashboard.monthly-sales-table');
  }
}
