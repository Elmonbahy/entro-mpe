<?php

use App\Http\Controllers\MutationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Fakturis\BrandController;
use App\Http\Controllers\Fakturis\GroupController;
use App\Http\Controllers\Fakturis\PelangganController;
use App\Http\Controllers\Fakturis\BarangController;
use App\Http\Controllers\Fakturis\SalesmanController;
use App\Http\Controllers\Fakturis\SupplierController;
use App\Http\Controllers\Fakturis\BeliController;
use App\Http\Controllers\Fakturis\BarangStockController;
use App\Http\Controllers\Fakturis\JualController;
use App\Http\Controllers\Fakturis\BarangStockAwalController;
use App\Http\Controllers\Fakturis\BarangRusakController;
use App\Http\Controllers\Fakturis\Laporan\FakturJual\LaporanFakturJualController;
use App\Http\Controllers\Fakturis\Laporan\Beli\LaporanBeliController;
use App\Http\Controllers\Fakturis\ReturController;

Route::resource('/barang', BarangController::class);
Route::resource('/brand', BrandController::class);
Route::resource('/group', GroupController::class);
Route::resource('/pelanggan', PelangganController::class);
Route::resource('/salesman', SalesmanController::class);
Route::resource('/supplier', SupplierController::class);

Route::prefix('export')->group(function () {
  Route::get('/barang', [BarangController::class, 'exportExcel'])->name('barang.export');
  Route::get('/brand', [BrandController::class, 'exportExcel'])->name('brand.export');
  Route::get('/group', [GroupController::class, 'exportExcel'])->name('group.export');
  Route::get('/pelanggan', [PelangganController::class, 'exportExcel'])->name('pelanggan.export');
  Route::get('/supplier', [SupplierController::class, 'exportExcel'])->name('supplier.export');
});

Route::prefix('beli')->as('beli.')->group(function () {
  Route::get('/', [BeliController::class, 'index'])->name('index');
  Route::post('/', [BeliController::class, 'store'])->name('store');
  Route::get('/create', [BeliController::class, 'create'])->name('create');
  Route::get('/{id}/add-item', [BeliController::class, 'addItem'])->name('add-item');
  Route::get('/{id}/edit', [BeliController::class, 'edit'])->name('edit');
  Route::get('/{id}', [BeliController::class, 'show'])->name('show');
  Route::patch('/{id}', [BeliController::class, 'update'])->name('update');
});

Route::prefix('stock')->as('stock.')->group(function () {
  Route::get('/', [BarangStockController::class, 'index'])->name('index');
  Route::get('/excel', [BarangStockController::class, 'exportExcel'])->name('excel');
  Route::get('/exportbarangperbatchExcel', [BarangStockController::class, 'exportbarangperbatchExcel'])->name('exportbarangperbatchExcel');
});

Route::prefix('stock-awal')->as('stock-awal.')->group(function () {
  Route::get('/', [BarangStockAwalController::class, 'index'])->name('index');
  Route::get('/create', [BarangStockAwalController::class, 'create'])->name('create');
  Route::patch('/store', [BarangStockAwalController::class, 'store'])->name('store');
  Route::get('/excel', [BarangStockAwalController::class, 'exportExcel'])->name('excel');
  Route::get('/{id}', [BarangStockAwalController::class, 'show'])->name('show');
});

Route::prefix('jual')->as('jual.')->group(function () {
  Route::get('/', [JualController::class, 'index'])->name('index');
  Route::post('/', [JualController::class, 'store'])->name('store');
  Route::get('/create', [JualController::class, 'create'])->name('create');
  Route::get('/{id}', [JualController::class, 'show'])->name('show');
  Route::get('/{id}/add-item', [JualController::class, 'addItem'])->name('add-item');
  Route::get('/{id}/edit', [JualController::class, 'edit'])->name('edit');
  Route::patch('/{id}/edit', [JualController::class, 'update'])->name('update');
});

Route::prefix('mutation')->as('mutation.')->group(function () {
  Route::get('/', [MutationController::class, 'index'])->name('index');
  Route::get('/excel', [MutationController::class, 'exportExcelMutation'])->name('excel-mutation');
  Route::get('/kartu-stock', [MutationController::class, 'kartuStock'])->name('kartu-stock');
});

Route::prefix('barang-rusak')->as('barang-rusak.')->group(function () {
  Route::get('/', [BarangRusakController::class, 'index'])->name('index');
  Route::get('/excel', [BarangRusakController::class, 'exportExcel'])->name('excel');
  Route::get('/{id}', [BarangRusakController::class, 'show'])->name('show');
});

Route::prefix('laporan-jual-faktur')->as('laporan-jual-faktur.')->group(function () {
  Route::get('/', [LaporanFakturJualController::class, 'index'])->name('index');
  Route::get('/excel', [LaporanFakturJualController::class, 'exportExcel'])->name('excel');
});

Route::prefix('laporan-beli')->as('laporan-beli.')->group(function () {
  Route::get('/', [LaporanBeliController::class, 'index'])->name('index');
  Route::get('/excel', [LaporanBeliController::class, 'exportExcel'])->name('excel');
});


Route::prefix('retur')->as('retur.')->group(function () {
  Route::post('/store', [ReturController::class, 'store'])->name('store');
});