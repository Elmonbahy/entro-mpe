<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Keuangan\PelangganController;
use App\Http\Controllers\Keuangan\SupplierController;
use App\Http\Controllers\Keuangan\BeliController;
use App\Http\Controllers\Keuangan\JualController;
use App\Http\Controllers\Cetak\TitipFakturController;
use App\Http\Controllers\Keuangan\Laporan\Beli\LaporanBeliController;
use App\Http\Controllers\Keuangan\Laporan\FakturJual\LaporanFakturJualController;
use App\Http\Controllers\Keuangan\Laporan\ListFakturBeli\LaporanListFakturBeliController;
use App\Http\Controllers\Keuangan\Laporan\ListFakturJual\LaporanListFakturJualController;

Route::resource('/pelanggan', PelangganController::class);
Route::resource('/supplier', SupplierController::class);

Route::prefix('export')->group(function () {
  Route::get('/pelanggan', [PelangganController::class, 'exportExcel'])->name('pelanggan.export');
  Route::get('/supplier', [SupplierController::class, 'exportExcel'])->name('supplier.export');
});

Route::prefix('beli')->as('beli.')->group(function () {
  Route::get('/', [BeliController::class, 'index'])->name('index');
  Route::get('/{id}', [BeliController::class, 'show'])->name('show');
  Route::patch('/{id}/payment', [BeliController::class, 'payment'])->name('payment');
});

Route::prefix('jual')->as('jual.')->group(function () {
  Route::get('/', [JualController::class, 'index'])->name('index');
  Route::get('/{id}', [JualController::class, 'show'])->name('show');
  Route::patch('/{id}', [JualController::class, 'update'])->name('update');
  Route::patch('/{id}/payment', [JualController::class, 'payment'])->name('payment');
});

Route::prefix('laporan-beli')->as('laporan-beli.')->group(function () {
  Route::get('/', [LaporanBeliController::class, 'index'])->name('index');
  Route::get('/excel', [LaporanBeliController::class, 'exportExcel'])->name('excel');
});

Route::prefix('laporan-list-faktur-beli')->as('laporan-list-faktur-beli.')->group(function () {
  Route::get('/', [LaporanListFakturBeliController::class, 'index'])->name('index');
  Route::get('/excel', [LaporanListFakturBeliController::class, 'exportExcel'])->name('excel');
});

Route::prefix('laporan-jual-faktur')->as('laporan-jual-faktur.')->group(function () {
  Route::get('/', [LaporanFakturJualController::class, 'index'])->name('index');
  Route::get('/excel', [LaporanFakturJualController::class, 'exportExcel'])->name('excel');
});

Route::prefix('laporan-list-faktur-jual')->as('laporan-list-faktur-jual.')->group(function () {
  Route::get('/', [LaporanListFakturJualController::class, 'index'])->name('index');
  Route::get('/excel', [LaporanListFakturJualController::class, 'exportExcel'])->name('excel');
});

Route::prefix('titip-faktur')->as('titip-faktur.')->group(function () {
  Route::get('/', [TitipFakturController::class, 'index'])->name('index');
  Route::get('/pdf', [TitipFakturController::class, 'exportPdf'])->name('pdf');
  Route::get('/{pelanggan_id}', [TitipFakturController::class, 'show'])->name('show');
});
