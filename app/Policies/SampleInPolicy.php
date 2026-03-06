<?php

namespace App\Policies;

use App\Enums\StatusBarangMasuk;
use App\Enums\StatusSample;
use App\Models\SampleIn;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SampleInPolicy
{
  public function update(User $user, SampleIn $sample_in)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function delete(User $user, SampleIn $sample_in)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    $isStockChanged = $sample_in->sampleinDetails()
      ->where('status_barang_masuk', '!=', StatusBarangMasuk::BELUM_LENGKAP)
      ->exists();

    if ($isStockChanged) {
      return Response::deny('Gagal! Barang sudah di approve gudang!');
    }

    return Response::allow();
  }

  public function sendToGudang(User $user, SampleIn $sample_in)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    if ($sample_in->sampleinDetails()->count() == 0) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function createSampleInDetail(User $user, SampleIn $sample_in)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function done(User $user, SampleIn $sample_in)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    if ($sample_in->status_sample === StatusSample::PROCESS_SAMPLE) {
      return Response::deny('Gagal! masih diproses fakturis!');
    }

    if ($sample_in->sampleinDetails()->count() == 0) {
      return Response::deny('Gagal! Belum ada pembelian barang!');
    }

    $belumLengkap = $sample_in->sampleinDetails->some(fn($sampleinDetail) => $sampleinDetail->status_barang_masuk === StatusBarangMasuk::BELUM_LENGKAP);

    if ($belumLengkap) {
      return Response::deny('Gagal! masih ada barang dengan status belum lengkap!');
    }

    return Response::allow();
  }

}
