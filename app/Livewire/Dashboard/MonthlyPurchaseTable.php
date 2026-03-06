<?php

namespace App\Livewire\Dashboard;

use App\Models\Beli;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class MonthlyPurchaseTable extends Component
{
  public $selectedYear;
  public $years = [];
  public $monthlyData = [];
  public $grandTotal = [];
  public $lastUpdated;

  public function mount()
  {
    $this->selectedYear = now()->year;

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
    $cacheKey = "monthly_purchase_{$this->selectedYear}";

    // Ambil dari cache jika ada
    $cached = Cache::get($cacheKey);
    if ($cached) {
      $this->monthlyData = $cached['monthly'] ?? [];
      $this->grandTotal = $cached['grand'] ?? [];
      $this->lastUpdated = $cached['updated_at'] ?? now()->format('d-m-Y H:i:s');
      return;
    }

    // Hitung ulang dari database jika tidak ada cache
    $query = Beli::with('beliDetails')
      ->select('id', 'tgl_faktur', 'bayar')
      ->whereYear('tgl_faktur', $this->selectedYear)
      ->orderBy('tgl_faktur');

    $data = $query->get()
      ->groupBy(fn($item) => Carbon::parse($item->tgl_faktur)->month)
      ->map(function ($group, $month) {
        $monthName = Carbon::create()->month($month)->translatedFormat('F');

        $total_faktur = $group->sum(
          fn($beli) =>
          $beli->total_faktur ?? $beli->beliDetails->sum('total_tagihan')
        );

        $total_lunas = $group->filter(
          fn($beli) =>
          $beli->bayar >= ($beli->total_faktur ?? $beli->beliDetails->sum('total_tagihan'))
        )->sum(
            fn($beli) =>
            $beli->total_faktur ?? $beli->beliDetails->sum('total_tagihan')
          );

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
    ];

    // Simpan cache + timestamp
    $cachedData = [
      'monthly' => $data,
      'grand' => $grandTotal,
      'updated_at' => now()->format('d-m-Y H:i:s'),
    ];

    Cache::put($cacheKey, $cachedData, now()->endOfDay());

    $this->monthlyData = $data;
    $this->grandTotal = $grandTotal;
    $this->lastUpdated = $cachedData['updated_at'];
  }

  public function refreshData()
  {
    Cache::forget("monthly_purchase_{$this->selectedYear}");
    $this->loadData();
  }

  public function render()
  {
    return view('livewire.dashboard.monthly-purchase-table');
  }
}
