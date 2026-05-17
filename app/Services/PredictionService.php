<?php

namespace App\Services;

use App\Models\Mutation;
use Carbon\Carbon;

class PredictionService
{
  /**
   * Prediksi bulan depan secara dinamis berdasarkan data historis
   */
  public function predictNextMonth($barangId, $alpha = 0.5)
  {
    $predictions = [];
    $dataHistoris = [];

    // Ambil data 3 bulan terakhir dari bulan berjalan
    for ($i = 1; $i <= 3; $i++) {
      $targetDate = Carbon::now()->subMonths($i);

      $totalKeluar = Mutation::where('barang_id', $barangId)
        // GUNAKAN kolom fisik 'mutationable_type'
        // Gunakan Full Namespace Class atau string yang tersimpan di DB
        ->where('mutationable_type', 'App\Models\JualDetail')
        ->whereMonth('tgl_mutation', $targetDate->month)
        ->whereYear('tgl_mutation', $targetDate->year)
        ->sum('stock_keluar');

      $dataHistoris[] = (float) $totalKeluar;
    }

    // Bobot MPE (Urutan: 1 bulan lalu, 2 bulan lalu, 3 bulan lalu)
    // Hasil normalisasi alpha 0.5: [0.571, 0.286, 0.143]
    $weights = [0.571, 0.286, 0.143];

    $forecastValue = 0;
    foreach ($weights as $index => $w) {
      $forecastValue += ($w * ($dataHistoris[$index] ?? 0));
    }

    return [
      'forecast' => round($forecastValue),
      'history' => array_reverse($dataHistoris), // Untuk grafik (Jan, Feb, Mar)
      'labels' => $this->getLabels()
    ];
  }

  private function getLabels()
  {
    return [
      Carbon::now()->subMonths(3)->format('M Y'),
      Carbon::now()->subMonths(2)->format('M Y'),
      Carbon::now()->subMonths(1)->format('M Y'),
      'Prediksi ' . Carbon::now()->addMonth()->format('M Y')
    ];
  }
}