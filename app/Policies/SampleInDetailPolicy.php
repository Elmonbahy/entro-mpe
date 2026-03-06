<?php

namespace App\Policies;

use App\Enums\StatusBarangMasuk;
use App\Enums\StatusSample;
use App\Models\SampleInDetail;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SampleInDetailPolicy
{


  public function delete(User $user, SampleInDetail $sample_in_detail)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    if ($sample_in_detail->status_barang_masuk === StatusBarangMasuk::LENGKAP) {
      return Response::deny('Gagal! Status barang masuk sudah lengkap!');
    }

    if ($sample_in_detail->jumlah_barang_masuk > 0) {
      return Response::deny('Gagal! Sudah ada barang masuk!');
    }

    return Response::allow();
  }

  public function edit(User $user, SampleInDetail $sample_in_detail)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function retur(User $user, SampleInDetail $sample_in_detail)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    if ($sample_in_detail->samplein->status_sample === StatusSample::PROCESS_SAMPLE) {
      return Response::deny('Gagal! masih diproses fakturis.');
    }

    if ($sample_in_detail->status_barang_masuk === StatusBarangMasuk::BELUM_LENGKAP) {
      return Response::deny('Gagal! Status barang masuk belum lengkap.');
    }

    return Response::allow();
  }

  public function stock(User $user, SampleInDetail $sample_in_detail)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    if ($sample_in_detail->samplein->status_sample === StatusSample::PROCESS_SAMPLE || $sample_in_detail->samplein->status_sample === StatusSample::PROCESS_SAMPLE) {
      return Response::deny('Gagal! masih diproses fakturis atau sudah selesai!');
    }

    if ($sample_in_detail->status_barang_masuk === StatusBarangMasuk::LENGKAP) {
      return Response::deny('Gagal! Status barang masuk sudah lengkap.');
    }

    return Response::allow();
  }
}
