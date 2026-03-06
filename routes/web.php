<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

// Auth Routes - only access if not authenticated
Route::middleware('guest')->group(function () {
  Route::get('/', function () {
    return redirect()->route('login');
  });
  Route::get('login', [AuthController::class, 'showLogin'])->name('login');
  Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1'); // Limit access 10x per minutes
});

// App Routes - only access if authenticated
Route::middleware('auth')->group(function () {
  Route::post('logout', [AuthController::class, 'logout'])->name('logout');
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

  // Super user routes
  Route::middleware([CheckRole::class . ':su'])->group(function () {
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
  });
});

Route::middleware(['auth', CheckRole::class . ':as'])
  ->prefix('supervisor')
  ->as('supervisor.')
  ->group(function () {
    require base_path('routes/web-supervisor.php');
  });

Route::middleware(['auth', CheckRole::class . ':af'])
  ->prefix('fakturis')
  ->as('fakturis.')
  ->group(function () {
    require base_path('routes/web-fakturis.php');
  });

Route::middleware(['auth', CheckRole::class . ':ag'])
  ->prefix('gudang')
  ->as('gudang.')
  ->group(function () {
    require base_path('routes/web-gudang.php');
  });

Route::middleware(['auth', CheckRole::class . ':aw'])
  ->prefix('warehouse')
  ->as('warehouse.')
  ->group(function () {
    require base_path('routes/web-warehouse.php');
  });

Route::middleware(['auth', CheckRole::class . ':ak'])
  ->prefix('keuangan')
  ->as('keuangan.')
  ->group(function () {
    require base_path('routes/web-keuangan.php');
  });

Route::middleware(['auth', CheckRole::class . ':aa'])
  ->prefix('accounting')
  ->as('accounting.')
  ->group(function () {
    require base_path('routes/web-accounting.php');
  });

Route::middleware(['auth', CheckRole::class . ':al'])
  ->prefix('logistik')
  ->as('logistik.')
  ->group(function () {
    require base_path('routes/web-logistik.php');
  });
