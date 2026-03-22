<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleAccessController extends Controller
{
  public function index()
  {
    $roles = Role::withCount('users')
      ->where('slug', '!=', 'su')
      ->get();
    return view('pages.access.superadmin.index', compact('roles'));
  }

  public function toggle($id)
  {
    $role = Role::findOrFail($id);
    $role->is_active = !$role->is_active; // Switch status (1 jadi 0, 0 jadi 1)
    $role->save();

    $status = $role->is_active ? 'Dibuka' : 'Ditutup';
    return back()->with('success', "Akses untuk Role {$role->name} berhasil {$status}.");
  }
}
