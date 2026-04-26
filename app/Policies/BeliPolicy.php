<?php

namespace App\Policies;

use App\Enums\StatusBarangMasuk;
use App\Enums\StatusFaktur;
use App\Models\Beli;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BeliPolicy
{
  public function update(User $user, Beli $beli)
  {
    if (!$user->hasAnyRole(['af'])) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function delete(User $user, Beli $beli)
  {
    if (!$user->hasAnyRole(['af'])) {
      return Response::deny();
    }

    $isStockChanged = $beli->beliDetails()
      ->where('status_barang_masuk', '!=', StatusBarangMasuk::BELUM_LENGKAP)
      ->exists();

    if ($isStockChanged) {
      return Response::deny('Gagal! Barang sudah di approve gudang!');
    }

    return Response::allow();
  }

  public function sendToGudang(User $user, Beli $beli)
  {
    if (!$user->hasAnyRole(['af'])) {
      return Response::deny();
    }

    if ($beli->beliDetails()->count() == 0) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function createBeliDetail(User $user, Beli $beli)
  {
    if (!$user->hasAnyRole(['af'])) {
      return Response::deny();
    }

    return Response::allow();
  }

  public function done(User $user, Beli $beli)
  {
    if (!$user->hasRole('ag')) {
      return Response::deny();
    }

    if ($beli->status_faktur === StatusFaktur::PROCESS_FAKTUR) {
      return Response::deny('Gagal! Faktur masih diproses fakturis!');
    }

    if ($beli->beliDetails()->count() == 0) {
      return Response::deny('Gagal! Belum ada pembelian barang!');
    }

    $belumLengkap = $beli->beliDetails->some(fn($beliDetail) => $beliDetail->status_barang_masuk === StatusBarangMasuk::BELUM_LENGKAP);

    if ($belumLengkap) {
      return Response::deny('Gagal! masih ada barang dengan status belum lengkap!');
    }

    return Response::allow();
  }

}
