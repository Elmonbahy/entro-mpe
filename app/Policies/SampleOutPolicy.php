<?php

namespace App\Policies;

use App\Enums\StatusBarangKeluar;
use App\Enums\StatusSample;
use App\Models\SampleOut;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SampleOutPolicy
{
  public function update(User $user, SampleOut $sample_out)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function delete(User $user, SampleOut $sample_out)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    $isStockChanged = $sample_out->sampleoutDetails()
      ->where('status_barang_keluar', '!=', StatusBarangKeluar::BELUM_LENGKAP)
      ->exists();

    if ($isStockChanged) {
      return Response::deny('Gagal! Barang sudah di approve gudang!');
    }

    return Response::allow();
  }

  public function sendToGudang(User $user, SampleOut $sample_out)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    if ($sample_out->sampleoutDetails()->count() == 0) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function createSampleOutDetail(User $user, SampleOut $sample_out)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function done(User $user, SampleOut $sample_out)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    if ($sample_out->status_sample === StatusSample::PROCESS_SAMPLE) {
      return Response::deny('Gagal! masih diproses fakturis!');
    }

    if ($sample_out->sampleoutDetails()->count() == 0) {
      return Response::deny('Gagal! Belum ada penjualan barang!');
    }

    $belumLengkap = $sample_out->sampleoutDetails->some(fn($sampleoutDetail) => $sampleoutDetail->status_barang_keluar == StatusBarangKeluar::BELUM_LENGKAP);

    if ($belumLengkap) {
      return Response::deny('Gagal! masih ada barang dengan status belum lengkap!');
    }

    return Response::allow();
  }

}
