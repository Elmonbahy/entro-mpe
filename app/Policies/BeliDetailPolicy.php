<?php

namespace App\Policies;

use App\Enums\StatusBarangMasuk;
use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Models\BeliDetail;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BeliDetailPolicy
{


  public function delete(User $user, BeliDetail $beli_detail)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    if ($beli_detail->beli->status_bayar === StatusBayar::PAID) {
      return Response::deny('Gagal! Faktur sudah lunas!');
    }

    if ($beli_detail->status_barang_masuk === StatusBarangMasuk::LENGKAP) {
      return Response::deny('Gagal! Status barang masuk sudah lengkap!');
    }

    if ($beli_detail->jumlah_barang_masuk > 0) {
      return Response::deny('Gagal! Sudah ada barang masuk!');
    }

    return Response::allow();
  }

  public function edit(User $user, BeliDetail $beli_detail)
  {
    if (!$user->hasAnyRole(['af', 'as'])) {
      return Response::deny();
    }

    if ($beli_detail->beli->status_bayar === StatusBayar::PAID) {
      return Response::deny('Gagal! Faktur sudah lunas!');
    }

    return Response::allow();
  }

  public function retur(User $user, BeliDetail $beli_detail)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    if ($beli_detail->beli->status_faktur === StatusFaktur::PROCESS_FAKTUR) {
      return Response::deny('Gagal! Faktur masih diproses fakturis.');
    }

    if ($beli_detail->status_barang_masuk === StatusBarangMasuk::BELUM_LENGKAP) {
      return Response::deny('Gagal! Status barang masuk belum lengkap.');
    }

    return Response::allow();
  }

  public function stock(User $user, BeliDetail $beli_detail)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    // if ($beli_detail->beli->status_bayar === StatusBayar::PAID) {
    //   return Response::deny('Gagal! Faktur sudah lunas!');
    // }

    if ($beli_detail->beli->status_faktur === StatusFaktur::PROCESS_FAKTUR || $beli_detail->beli->status_faktur === StatusFaktur::DONE) {
      return Response::deny('Gagal! Faktur masih diproses fakturis atau sudah selesai!');
    }

    if ($beli_detail->status_barang_masuk === StatusBarangMasuk::LENGKAP) {
      return Response::deny('Gagal! Status barang masuk sudah lengkap.');
    }

    return Response::allow();
  }
}
