<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\BeliController;
use App\Http\Controllers\Accounting\JualController;
use App\Http\Controllers\Accounting\BarangController;
use App\Http\Controllers\Accounting\MutationController;
use App\Http\Controllers\Accounting\BarangRusakController;
use App\Http\Controllers\Accounting\BarangStockAwalController;
use App\Http\Controllers\Accounting\SuratJalanController;
use App\Http\Controllers\Accounting\Laporan\Beli\LaporanBeliController;
use App\Http\Controllers\Accounting\Laporan\FakturJual\LaporanFakturJualController;
use App\Http\Controllers\Accounting\Laporan\ListFakturBeli\LaporanListFakturBeliController;
use App\Http\Controllers\Accounting\Laporan\ListFakturJual\LaporanListFakturJualController;

Route::resource('/barang', BarangController::class);

Route::prefix('export')->group(function () {
  Route::get('/barang', [BarangController::class, 'exportExcel'])->name('barang.export');
});

Route::prefix('beli')->as('beli.')->group(function () {
  Route::get('/', [BeliController::class, 'index'])->name('index');
  Route::get('/{id}', [BeliController::class, 'show'])->name('show');
});

Route::prefix('stock-awal')->as('stock-awal.')->group(function () {
  Route::get('/', [BarangStockAwalController::class, 'index'])->name('index');
  Route::get('/excel', [BarangStockAwalController::class, 'exportExcel'])->name('excel');
  Route::get('/{id}', [BarangStockAwalController::class, 'show'])->name('show');
});

Route::prefix('jual')->as('jual.')->group(function () {
  Route::get('/', [JualController::class, 'index'])->name('index');
  Route::get('/{id}', [JualController::class, 'show'])->name('show');
});

Route::prefix('surat-jalan')->as('surat-jalan.')->group(function () {
  Route::get('/', [SuratJalanController::class, 'index'])->name('index');
  Route::get('/excel', [SuratJalanController::class, 'exportExcel'])->name('excel');
  Route::get('/{id}', [SuratJalanController::class, 'show'])->name('show');
});

Route::prefix('mutation')->as('mutation.')->group(function () {
  Route::get('/kartu-stock', [MutationController::class, 'kartuStock'])->name('kartu-stock');
});

Route::prefix('barang-rusak')->as('barang-rusak.')->group(function () {
  Route::get('/', [BarangRusakController::class, 'index'])->name('index');
  Route::get('/excel', [BarangRusakController::class, 'exportExcel'])->name('excel');
  Route::get('/{id}', [BarangRusakController::class, 'show'])->name('show');
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
