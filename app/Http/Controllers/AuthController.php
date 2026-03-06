<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{

  public function showLogin()
  {
    return view('auth.login');
  }

  public function showRegister()
  {
    $roles = Role::all();
    return view('auth.register', compact('roles'));
  }

  private function makeLoginCredentials(Request $request)
  {
    $credentials = [
      'password' => $request->password
    ];

    if (filter_var($request->login, FILTER_VALIDATE_EMAIL)) {
      $credentials['email'] = $request->login;
    } else {
      $credentials['username'] = $request->login;
    }

    return $credentials;
  }

  public function login(Request $request)
  {
    $request->validate([
      'login' => 'required|string',
      'password' => 'required|string',
    ]);

    $user = User::where('email', $request->login)
      ->orWhere('username', $request->login)
      ->first();

    if (!$user) {
      return back()
        ->withInput()
        ->withErrors(['login' => 'Invalid credentials, please try again.']);
    }

    // Account is locked
    if ($user->locked_until) {
      if (now()->greaterThan($user->locked_until)) {
        $user->login_attempts = 0;
        $user->locked_until = null;
        $user->save();
      } else {
        $minutesLeft = round(now()->diffInMinutes($user->locked_until, true), 2);

        return back()
          ->withInput()
          ->withErrors(['login' => "Too many failed attempts. Your account is locked for {$minutesLeft} minutes."]);
      }
    }

    // Attempt login
    if (Auth::attempt($this->makeLoginCredentials($request))) {
      $user->login_attempts = 0;
      $user->locked_until = null;
      $user->save();
      $request->session()->regenerate();
      return redirect()->intended('dashboard');
    }

    // Failed login attempt, increase login attempts count
    $user->increment('login_attempts');

    if ($user->login_attempts >= 5) {
      $user->locked_until = now()->addMinutes(30);
      $user->save();
    }

    return back()
      ->withInput()
      ->withErrors(['login' => 'Invalid credentials, please try again.']);
  }

  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login')->with('success', 'Berhasil logout.');
  }

  public function register(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'username' => 'required|string|max:60|unique:users',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
      'role' => 'required|exists:roles,id'
    ]);

    User::create([
      'name' => $request->name,
      'username' => $request->username,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role_id' => $request->role
    ]);

    return redirect()->route('user.index')->with('success', 'Berhasil menambahkan user.');
  }
}
