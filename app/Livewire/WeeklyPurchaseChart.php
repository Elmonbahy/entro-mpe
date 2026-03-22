<?php

namespace App\Livewire;

use App\Models\Beli;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class WeeklyPurchaseChart extends Component
{
  // Properties are identical to the sales chart
  public $startMonth;
  public $endMonth;
  public $currentYear;
  public $chartData = [];

  public Collection $availableMonths;

  public function mount()
  {
    $this->currentYear = now()->year;
    $this->availableMonths = collect(range(1, 12))->mapWithKeys(function ($month) {
      return [$month => Carbon::create(null, $month, 1)->translatedFormat('F')];
    });

    // Set initial month range
    $this->endMonth = (int) now()->month;
    $this->startMonth = (int) max(1, $this->endMonth - 2);

    $this->loadChartData();
  }

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
      $this->dispatch('updateChartBeli', data: $this->chartData);
      return;
    }

    // ⭐ CHANGE 1: Target the Beli model
    $query = Beli::with('beliDetails')
      ->selectRaw('
                FLOOR((DAY(tgl_terima_faktur) - 1) / 7) + 1 as week_of_month,
                MONTH(tgl_terima_faktur) as month,
                YEAR(tgl_terima_faktur) as year,
                id,
                ppn,
                bayar,
                tgl_terima_faktur
            ')
      // ⭐ CHANGE 2: Filter by 'tgl_terima_faktur' (date the invoice was received)
      ->whereBetween('tgl_terima_faktur', [$startRange, $endRange]);

    $result = $query->get()
      ->groupBy(function ($item) {
        // Grouping uses the invoice received date
        return Carbon::parse($item->tgl_terima_faktur)->weekOfYear . '-' . $item->year;
      })
      ->map(function ($group) {
        $firstItem = $group->first();

        // Use Beli model accessors: getTotalTagihanAttribute and getTotalTerbayarAttribute
        $total_tagihan = $group->sum(function ($item) {
          return $item->total_tagihan;
        });
        $total_terbayar = $group->sum(function ($item) {
          return $item->total_terbayar;
        });

        $weekNumber = Carbon::parse($firstItem->tgl_terima_faktur)->weekOfYear;
        $monthName = Carbon::parse($firstItem->tgl_terima_faktur)->translatedFormat('M');

        return [
          'label' => "W{$weekNumber} ({$monthName})",
          'total_tagihan' => (string) ($total_tagihan ?? 0),
          'total_terbayar' => (string) ($total_terbayar ?? 0)
        ];
      })
      ->values()
      ->toArray();

    $this->chartData = $result;

    // ⭐ CHANGE 3: Use a unique dispatch event name
    $this->dispatch('updateChartBeli', data: $this->chartData);
  }

  public function render()
  {
    return view('livewire.weekly-purchase-chart');
  }
}