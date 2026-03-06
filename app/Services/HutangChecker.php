<?php

namespace App\Services;

use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Models\Jual;
use App\Models\Pelanggan;
use Carbon\Carbon;

class HutangChecker
{
  /**
   * Check if a pelanggan can create a new jual based on plafon hutang and limit hari.
   *
   * @param int $pelangganId
   * @return void throw if not allow to create jual
   */

  public static function validateHutang(int $pelangganId)
  {
    $pelanggan = Pelanggan::findOrFail($pelangganId);

    $lastJual = Jual::where('pelanggan_id', $pelangganId)
      ->where('status_bayar', StatusBayar::UNPAID)
      ->where('status_faktur', StatusFaktur::DONE)
      ->latest('tgl_faktur')
      ->first();

    if ($lastJual) {
      $totalHutang = Jual::where('pelanggan_id', $pelangganId)
        ->where('status_bayar', StatusBayar::UNPAID)
        ->where('status_faktur', StatusFaktur::DONE)
        ->get()
        ->sum(function ($jual) {
          return $jual->total_tagihan - $jual->total_terbayar;
        });

      $plafon = (float) $pelanggan->plafon_hutang;
      $limit = (int) $pelanggan->limit_hari;

      // Check plafon hutang
      if ($plafon && $totalHutang >= $plafon) {
        throw new \Exception('Total tagihan melebihi plafon hutang.');
      }

      // Check limit hari
      if ($limit && Carbon::parse($lastJual->tgl_faktur)->addDays($limit)->isPast()) {
        throw new \Exception('Batas waktu pembayaran hutang terakhir telah lewat.');
      }
    }
  }
}
