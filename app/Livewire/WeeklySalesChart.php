<?php

namespace App\Livewire;

use App\Models\Jual;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

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

    // Set initial month range (e.g., current month only or a default quarter)
    $this->endMonth = (int) now()->month;
    $this->startMonth = (int) max(1, $this->endMonth - 2);

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

    $startMonth = (int) $this->startMonth;
    $endMonth = (int) $this->endMonth;

    $startRange = Carbon::create($this->currentYear, $startMonth, 1)->startOfMonth()->startOfDay();
    $endRange = Carbon::create($this->currentYear, $endMonth, 1)->endOfMonth()->endOfDay();

    if ($startRange->gt($endRange)) {
      $this->chartData = [];
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

    $result = $query->get()
      ->groupBy(function ($item) {
        // Grouping by week number within the year for unique points
        return Carbon::parse($item->tgl_faktur)->weekOfYear . '-' . $item->year;
      })
      ->map(function ($group) {
        $firstItem = $group->first();

        // Calculate total tagihan and terbayar using the model accessors
        $total_tagihan = $group->sum(function ($item) {
          return $item->total_tagihan;
        });
        $total_terbayar = $group->sum(function ($item) {
          return $item->total_terbayar;
        });

        // Create a label combining week and month name
        $weekNumber = Carbon::parse($firstItem->tgl_faktur)->weekOfYear;
        $monthName = Carbon::parse($firstItem->tgl_faktur)->translatedFormat('M');

        return [
          'label' => "W{$weekNumber} ({$monthName})",
          'total_tagihan' => (string) ($total_tagihan ?? 0),
          'total_terbayar' => (string) ($total_terbayar ?? 0)
        ];
      })
      ->values()
      ->toArray();

    $this->chartData = $result;

    $this->dispatch('updateChart', data: $this->chartData);
  }

  public function render()
  {
    return view('livewire.weekly-sales-chart');
  }
}