<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\User;

class UsersController extends Controller
{
  public function index()
  {
    $threshold = now()->subMinutes(5)->getTimestamp();

    $users = User::with(['role'])
      ->addSelect([
        // Mengambil satu data terakhir dari tabel session secara instan
        'last_interaction' => \DB::table('sessions')
          ->select('last_activity')
          ->whereColumn('user_id', 'users.id')
          ->orderBy('last_activity', 'desc')
          ->limit(1)
      ])
      ->orderBy('last_interaction', 'desc')
      ->get();

    return view('pages.user.supervisor.index', compact('users', 'threshold'));
  }
}
