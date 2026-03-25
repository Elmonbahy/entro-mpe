<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
  public function handle(Request $request, Closure $next, ...$roles)
  {
    // 1. Cek Autentikasi
    if (!auth()->check()) {
      return redirect('login');
    }

    $user = auth()->user();
    $role = $user->role;

    // 2. Validasi Keberadaan Role
    if (!$role || !isset($role->slug)) {
      abort(403, 'Unauthorized action.');
    }

    // 3. Logika Lockdown (Cek status aktif)
    // Pastikan kolom di database memang bernama 'is_active'
    if (isset($role->is_active) && !$role->is_active) {
      // Izinkan Superadmin tetap masuk
      if ($role->slug !== 'su') {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('error', 'Akses divisi Anda sedang ditutup sementara (Stock Opname).');
      }
    }

    // 4. Cek Hak Akses Rute (RBAC)
    $userRole = $role->slug;
    if (!empty($roles) && !in_array($userRole, $roles)) {
      abort(403, 'Unauthorized action.');
    }

    return $next($request);
  }
}
