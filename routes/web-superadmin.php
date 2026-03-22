<?php

use App\Http\Controllers\KomoditiController;
use App\Http\Controllers\Superadmin\BarangController;
use App\Http\Controllers\Superadmin\GolonganKomoditiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\SalesmanController;
use App\Http\Controllers\Superadmin\BarangStockAwalController;
use App\Http\Controllers\Superadmin\RoleAccessController;

Route::prefix('manage-access')->as('manage-access.')->group(function () {
  Route::get('/', [RoleAccessController::class, 'index'])->name('index');
  Route::patch('/manage-access/{id}/toggle', [RoleAccessController::class, 'toggle'])->name('toggle');
});