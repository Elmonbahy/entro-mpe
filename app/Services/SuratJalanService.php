<?php

namespace App\Services;

use App\Models\Jual;
use App\Models\JualDetail;
use App\Models\SuratJalanDetail;
use App\Enums\StatusKirim;
use App\Models\BarangRetur;

class SuratJalanService
{
  public function updateStatusKirimBySuratJalan(int $surat_jalan_id): void
  {
    $jualDetailIds = SuratJalanDetail::where('surat_jalan_id', $surat_jalan_id)
      ->pluck('jual_detail_id')
      ->filter()
      ->unique()
      ->toArray();

    if (empty($jualDetailIds)) {
      return;
    }

    $jualIds = JualDetail::whereIn('id', $jualDetailIds)
      ->pluck('jual_id')
      ->unique()
      ->toArray();

    foreach ($jualIds as $jualId) {
      $this->updateStatusKirimByJual($jualId);
    }
  }


  public function updateStatusKirimByJual(int $jual_id): void
  {
    $jual = Jual::find($jual_id);
    if (!$jual)
      return;

    $jualDetails = $jual->jualDetails;
    $totalKeluar = $jualDetails->sum('jumlah_barang_keluar');

    $jualDetailIds = $jualDetails->pluck('id')->toArray();
    $totalTerkirim = SuratJalanDetail::whereIn('jual_detail_id', $jualDetailIds)->sum('jumlah_barang_dikirim');

    // Hitung total retur dari semua jualDetails
    $totalRetur = $jualDetails->sum(function ($detail) {
      return $detail->returs->sum('jumlah_barang_retur');
    });

    // Total barang keluar yang sudah dikirim (kurangi retur)
    $netTerkirim = $totalTerkirim - $totalRetur;

    if ($netTerkirim >= $totalKeluar && $totalKeluar > 0) {
      $jual->status_kirim = StatusKirim::SHIPPED;
    } elseif ($netTerkirim > 0) {
      $jual->status_kirim = StatusKirim::PARTIAL;
    } else {
      $jual->status_kirim = StatusKirim::PENDING;
    }

    $jual->save();
  }

}
