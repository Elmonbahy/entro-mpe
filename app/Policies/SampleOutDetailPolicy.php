<?php

namespace App\Policies;

use App\Enums\StatusBarangKeluar;
use App\Enums\StatusSample;
use App\Models\SampleOutDetail;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SampleOutDetailPolicy
{
  public function delete(User $user, SampleOutDetail $sample_out_detail)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    if ($sample_out_detail->status_barang_keluar === StatusBarangKeluar::LENGKAP) {
      return Response::deny('Gagal! Status barang keluar sudah lengkap.');
    }

    if ($sample_out_detail->jumlah_barang_keluar > 0) {
      return Response::deny('Gagal! Sudah ada barang keluar!');
    }

    return Response::allow();
  }
  public function edit(User $user, SampleOutDetail $sample_out_detail)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function retur(User $user, SampleOutDetail $sample_out_detail)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    if ($sample_out_detail->sampleout->status_sample === StatusSample::PROCESS_SAMPLE) {
      return Response::deny('Gagal! masih diproses fakturis!');
    }

    if ($sample_out_detail->jumlah_barang_keluar == 0) {
      return Response::deny('Gagal! Belum ada barang keluar!');
    }

    return Response::allow();
  }


  public function stock(User $user, SampleOutDetail $sample_out_detail)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    if ($sample_out_detail->sampleout->status_sample === StatusSample::PROCESS_SAMPLE) {
      return Response::deny('Gagal!  masih diproses fakturis!');
    }

    if ($sample_out_detail->status_barang_keluar === StatusBarangKeluar::LENGKAP) {
      return Response::deny('Gagal! Status barang keluar sudah lengkap.');
    }

    return Response::allow();
  }
}
