<?php

namespace App\Policies;

use App\Enums\StatusBarangKeluar;
use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Models\JualDetail;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JualDetailPolicy
{
  public function delete(User $user, JualDetail $jual_detail)
  {
    if (!$user->hasAnyRole(['af'])) {
      return Response::deny();
    }

    if ($jual_detail->status_barang_keluar === StatusBarangKeluar::LENGKAP) {
      return Response::deny('Gagal! Status barang keluar sudah lengkap.');
    }

    if ($jual_detail->jual->status_bayar === StatusBayar::PAID) {
      return Response::deny('Gagal! Faktur sudah lunas!');
    }

    if ($jual_detail->jumlah_barang_keluar > 0) {
      return Response::deny('Gagal! Sudah ada barang keluar!');
    }

    return Response::allow();
  }
  public function edit(User $user, JualDetail $jual_detail)
  {
    if (!$user->hasAnyRole(['af'])) {
      return Response::deny();
    }

    if ($jual_detail->jual->status_bayar === StatusBayar::PAID) {
      return Response::deny('Gagal! Faktur sudah lunas!');
    }

    return Response::allow();
  }

  public function retur(User $user, JualDetail $jual_detail)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    if ($jual_detail->jual->status_faktur === StatusFaktur::PROCESS_FAKTUR) {
      return Response::deny('Gagal! Faktur masih diproses fakturis!');
    }

    if ($jual_detail->jumlah_barang_keluar == 0) {
      return Response::deny('Gagal! Belum ada barang keluar!');
    }

    return Response::allow();
  }


  public function stock(User $user, JualDetail $jual_detail)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    if ($jual_detail->jual->status_faktur === StatusFaktur::PROCESS_FAKTUR) {
      return Response::deny('Gagal! Faktur masih diproses fakturis!');
    }

    if ($jual_detail->status_barang_keluar === StatusBarangKeluar::LENGKAP) {
      return Response::deny('Gagal! Status barang keluar sudah lengkap.');
    }

    return Response::allow();
  }
}
