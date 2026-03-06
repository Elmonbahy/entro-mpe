<?php

namespace App\Livewire\Dashboard;

use App\Models\Jual;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class WeeklySalesChart extends Component
{
  public $startMonth;
  public $endMonth;
  public $currentYear;
  public $chartData = [];

  // All available months for the dropdown
  public Collection $availableMonths;

  public function mount()
  {
    $this->currentYear = now()->year;
    $this->availableMonths = collect(range(1, 12))->mapWithKeys(function ($month) {
      return [$month => Carbon::create(null, $month, 1)->translatedFormat('F')];
    });

    // Tampilkan hanya bulan berjalan saat pertama kali load
    $thisMonth = (int) now()->month;
    $this->startMonth = $thisMonth;
    $this->endMonth = $thisMonth;

    $this->loadChartData();
  }


  // Runs automatically when $startMonth or $endMonth changes
  public function updated($propertyName)
  {
    $this->startMonth = (int) $this->startMonth;
    $this->endMonth = (int) $this->endMonth;

    if ($this->endMonth < $this->startMonth) {
      if ($propertyName === 'startMonth') {
        $this->endMonth = $this->startMonth;
      } else {
        $this->startMonth = $this->endMonth;
      }
    }

    $this->loadChartData();
  }

  public function loadChartData()
  {
    $user = Auth::user();

    $startMonth = (int) $this->startMonth;
    $endMonth = (int) $this->endMonth;

    $startRange = Carbon::create($this->currentYear, $startMonth, 1)->startOfMonth()->startOfDay();
    $endRange = Carbon::create($this->currentYear, $endMonth, 1)->endOfMonth()->endOfDay();

    if ($startRange->gt($endRange)) {
      $this->chartData = [];
      $this->dispatch('updateChart', data: $this->chartData);
      return;
    }

    // Buat cache key berdasarkan user, tahun, bulan mulai dan bulan akhir
    $cacheKey = 'weekly_sales_chart_' . $user->id . "_{$this->currentYear}_{$startMonth}_{$endMonth}";

    // Cek cache terlebih dahulu
    if (Cache::has($cacheKey)) {
      $this->chartData = Cache::get($cacheKey);
      $this->dispatch('updateChart', data: $this->chartData);
      return;
    }

    $query = Jual::with('jualDetails')
      ->selectRaw('
            FLOOR((DAY(tgl_faktur) - 1) / 7) + 1 as week_of_month,
            MONTH(tgl_faktur) as month,
            YEAR(tgl_faktur) as year,
            id,
            is_pungut_ppn,
            bayar,
            tgl_faktur
        ')
      ->whereBetween('tgl_faktur', [$startRange, $endRange]);

    // Apply branch scope manually since this is a custom query
    if (!$user->hasAnyRole(['su', 'as']) && $user->branch_id) {
      $query->where('branch_id', $user->branch_id);
    }

    $result = $query->get()
      ->groupBy(function ($item) {
        return Carbon::parse($item->tgl_faktur)->weekOfYear . '-' . $item->year;
      })
      ->map(function ($group) {
        $firstItem = $group->first();
        $total_tagihan = $group->sum(fn($item) => $item->total_tagihan);
        $total_terbayar = $group->sum(fn($item) => $item->total_terbayar);
        $weekNumber = Carbon::parse($firstItem->tgl_faktur)->weekOfYear;
        $monthName = Carbon::parse($firstItem->tgl_faktur)->translatedFormat('M');

        return [
          'label' => "W{$weekNumber} ({$monthName})",
          'total_tagihan' => (string) ($total_tagihan ?? 0),
          'total_terbayar' => (string) ($total_terbayar ?? 0),
        ];
      })
      ->values()
      ->toArray();

    $this->chartData = $result;

    // Simpan data ke cache sampai akhir hari ini
    Cache::put($cacheKey, $this->chartData, now()->endOfDay());

    $this->dispatch('updateChart', data: $this->chartData);
  }

  public function render()
  {
    return view('livewire.dashboard.weekly-sales-chart');
  }
}