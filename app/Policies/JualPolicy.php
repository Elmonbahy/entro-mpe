<?php

namespace App\Policies;

use App\Enums\StatusBarangKeluar;
use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Models\Jual;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JualPolicy
{
  public function update(User $user, Jual $jual)
  {
    if (!$user->hasAnyRole(['af'])) {
      return Response::deny();
    }

    // if ($jual->status_bayar === StatusBayar::PAID) {
    //   return Response::deny('Gagal! Faktur sudah lunas!');
    // }

    // if ($jual->total_terbayar > 0) {
    //   return Response::deny('Gagal! Faktur sudah dibayar!');
    // }

    return Response::allow();
  }

  public function delete(User $user, Jual $jual)
  {
    if (!$user->hasAnyRole(['af'])) {
      return Response::deny();
    }

    if ($jual->status_bayar === StatusBayar::PAID) {
      return Response::deny('Gagal! Faktur sudah lunas!');
    }

    if ($jual->total_terbayar > 0) {
      return Response::deny('Gagal! Faktur sudah dibayar!');
    }

    $isStockChanged = $jual->jualDetails()
      ->where('status_barang_keluar', '!=', StatusBarangKeluar::BELUM_LENGKAP)
      ->exists();

    if ($isStockChanged) {
      return Response::deny('Gagal! Barang sudah di approve gudang!');
    }

    return Response::allow();
  }

  public function sendToGudang(User $user, Jual $jual)
  {
    if (!$user->hasAnyRole(['af'])) {
      return Response::deny();
    }

    if ($jual->jualDetails()->count() == 0) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function createJualDetail(User $user, Jual $jual)
  {
    if (!$user->hasAnyRole(['af'])) {
      return Response::deny();
    }

    // if ($jual->status_bayar === StatusBayar::PAID) {
    //   return Response::deny('Gagal! Faktur sudah lunas!');
    // }

    // if ($jual->total_terbayar > 0) {
    //   return Response::deny('Gagal! Faktur sudah dibayar!');
    // }

    return Response::allow();
  }

  public function done(User $user, Jual $jual)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    if ($jual->status_faktur === StatusFaktur::PROCESS_FAKTUR) {
      return Response::deny('Gagal! Faktur masih diproses fakturis!');
    }

    if ($jual->jualDetails()->count() == 0) {
      return Response::deny('Gagal! Belum ada penjualan barang!');
    }

    $belumLengkap = $jual->jualDetails->some(fn($jualDetail) => $jualDetail->status_barang_keluar == StatusBarangKeluar::BELUM_LENGKAP);

    if ($belumLengkap) {
      return Response::deny('Gagal! masih ada barang dengan status belum lengkap!');
    }

    return Response::allow();
  }

  public function payment(User $user, Jual $jual)
  {
    if (!$user->hasRole('ak')) {
      return Response::deny();
    }

    if ($jual->status_bayar === StatusBayar::PAID) {
      return Response::deny('Gagal! Faktur sudah lunas!');
    }

    return Response::allow();
  }
}
