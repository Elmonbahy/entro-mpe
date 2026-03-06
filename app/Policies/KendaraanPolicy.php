<?php

namespace App\Policies;

use App\Models\Kendaraan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class KendaraanPolicy
{

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, Kendaraan $kendaraan) : bool
  {
    if (! $user->hasRole('al')) {
      return \Response::deny();
    }

    return \Response::allow();
  }

}
