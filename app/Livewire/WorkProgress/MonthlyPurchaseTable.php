<?php

namespace App\Livewire\WorkProgress;

use App\Enums\StatusBayar;
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

    // Tambahkan filter status_faktur DONE di sini
    $this->years = Beli::where('status_faktur', 'DONE') // <-- Penambahan Filter
      ->selectRaw('YEAR(tgl_faktur) as year')
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
    $cached = Cache::get($cacheKey);

    if ($cached) {
      $this->monthlyData = $cached['monthly'] ?? [];
      $this->grandTotal = $cached['grand'] ?? [];
      $this->lastUpdated = $cached['updated_at'] ?? now()->format('d-m-Y H:i:s');
      return;
    }

    // Gunakan withSum untuk performa lebih cepat jika data sangat besar
    $data = Beli::with(['beliDetails'])
      ->where('status_faktur', 'DONE')
      ->whereYear('tgl_faktur', $this->selectedYear)
      ->get()
      ->groupBy(fn($item) => Carbon::parse($item->tgl_faktur)->month)
      ->map(function ($group, $month) {

        $total_faktur_bulan_ini = 0;
        $total_lunas_bulan_ini = 0;
        $total_belum_lunas_bulan_ini = 0;

        foreach ($group as $beli) {
          // Mengambil nilai asli desimal tanpa pembulatan round()
          $nilaiBarang = $beli->total_faktur;
          $nominalTerbayar = $beli->total_terbayar;

          if ($beli->status_bayar === StatusBayar::PAID) {
            // Jika lunas, gunakan nilai barang asli
            $lunas = $nilaiBarang;
            $sisa = 0;
          } else {
            $lunas = $nominalTerbayar;
            // Sisa dihitung dengan presisi desimal
            $sisa = max(0, $nilaiBarang - $nominalTerbayar);
          }

          $total_faktur_bulan_ini += $nilaiBarang;
          $total_lunas_bulan_ini += $lunas;
          $total_belum_lunas_bulan_ini += $sisa;
        }

        return [
          'month_name' => Carbon::create()->month($month)->translatedFormat('F'),
          'total_faktur' => $total_faktur_bulan_ini,
          'total_lunas' => $total_lunas_bulan_ini,
          'total_belum_lunas' => $total_belum_lunas_bulan_ini,
          'persentase_lunas' => $total_faktur_bulan_ini > 0
            ? round(($total_lunas_bulan_ini / $total_faktur_bulan_ini) * 100, 2)
            : 0,
        ];
      })
      ->values()
      ->toArray();

    // Kalkulasi Grand Total menggunakan nilai desimal yang akumulatif
    $grand = [
      'total_faktur' => array_sum(array_column($data, 'total_faktur')),
      'total_lunas' => array_sum(array_column($data, 'total_lunas')),
      'total_belum_lunas' => array_sum(array_column($data, 'total_belum_lunas')),
    ];

    $this->monthlyData = $data;
    $this->grandTotal = $grand;
    $this->lastUpdated = now()->format('d-m-Y H:i:s');

    Cache::put($cacheKey, [
      'monthly' => $this->monthlyData,
      'grand' => $this->grandTotal,
      'updated_at' => $this->lastUpdated,
    ], now()->addHours(2)); // Simpan 2 jam saja agar data lebih aktual
  }

  public function refreshData()
  {
    Cache::forget("monthly_purchase_{$this->selectedYear}");
    $this->loadData();
  }
  public function render()
  {
    return view('livewire.work-progress.monthly-purchase-table');
  }
}
