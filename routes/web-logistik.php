<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Logistik\PelangganController;
use App\Http\Controllers\Logistik\KendaraanController;
use App\Http\Controllers\Logistik\BeliController;
use App\Http\Controllers\Logistik\JualController;
use App\Http\Controllers\Logistik\SuratJalanController;

Route::resource('/pelanggan', PelangganController::class);
Route::resource('/kendaraan', KendaraanController::class);

Route::prefix('beli')->as('beli.')->group(function () {
  Route::get('/', [BeliController::class, 'index'])->name('index');
  Route::get('/{id}', [BeliController::class, 'show'])->name('show');
});

Route::prefix('jual')->as('jual.')->group(function () {
  Route::get('/', [JualController::class, 'index'])->name('index');
  Route::get('/{id}', [JualController::class, 'show'])->name('show');
});

Route::prefix('surat-jalan')->as('surat-jalan.')->group(function () {
  Route::get('/', [SuratJalanController::class, 'index'])->name('index');
  Route::post('/', [SuratJalanController::class, 'store'])->name('store');
  Route::get('/create', [SuratJalanController::class, 'create'])->name('create');
  Route::get('/{id}', [SuratJalanController::class, 'show'])->name('show');
  Route::get('/{id}/pdf', [SuratJalanController::class, 'exportPdf'])->name('pdf');
  Route::get('/{id}/add-item', [SuratJalanController::class, 'addItem'])->name('add-item');
});
