<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
  public function handle(Request $request, Closure $next, ...$roles)
  {
    // Check if the user is authenticated
    if (!auth()->check()) {
      return redirect('login');
    }

    // Ensure the user has a role assigned
    $user = auth()->user();
    if (!$user->role || !isset($user->role->slug)) {
      // Abort if role is missing or invalid
      abort(403, 'Unauthorized action.');
    }

    // Get the role of the authenticated user
    $userRole = $user->role->slug;

    // Check if the user's role is in the list of allowed roles & not empty
    if (!empty($roles) && in_array($userRole, $roles)) {
      // Allow request to proceed if role is authorized
      return $next($request);
    }

    // If the user's role is not in the allowed roles, return a 403 Unauthorized error
    abort(403, 'Unauthorized action.');
  }
}
