<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function index()
  {
    $users = User::with('role')->get();
    return view('pages.user.index', [
      'users' => $users
    ]);
  }

  public function destroy(int $id)
  {
    if (Auth::user()->id === $id) {
      abort(403);
    }

    $user = User::find($id);
    if ($user) {
      $user->delete();
    }

    return redirect()->route('user.index')->with('success', 'Berhasil menghapus data user.');
  }

}
