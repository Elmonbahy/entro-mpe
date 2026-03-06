<?php

namespace App\Policies;


use App\Models\User;
use App\Models\SuratJalan;
use Carbon\Carbon;
use Illuminate\Auth\Access\Response;

class SuratJalanPolicy
{
  public function update(User $user, SuratJalan $surat_jalan)
  {
    if (!$user->hasAnyRole(['ag'])) {
      return Response::deny();
    }

    $created = Carbon::parse($surat_jalan->created_at)->timezone('Asia/Makassar');
    $isSameDay = $created->isSameDay(now('Asia/Makassar'));

    if (!$isSameDay) {
      return Response::deny('Surat jalan hanya dapat diubah pada hari yang sama saat dibuat.');
    }

    return Response::allow();
  }


}
