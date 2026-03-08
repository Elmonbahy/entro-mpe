<?php

use App\Http\Controllers\Supervisor\MutationController;
use App\Http\Controllers\PersediaanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Supervisor\BrandController;
use App\Http\Controllers\Supervisor\GroupController;
use App\Http\Controllers\Supervisor\PelangganController;
use App\Http\Controllers\Supervisor\BarangController;
use App\Http\Controllers\Supervisor\SalesmanController;
use App\Http\Controllers\Supervisor\SupplierController;
use App\Http\Controllers\Supervisor\BeliController;
use App\Http\Controllers\Supervisor\BarangStockController;
use App\Http\Controllers\Supervisor\JualController;
use App\Http\Controllers\Supervisor\SuratJalanController;

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
});

Route::prefix('jual')->as('jual.')->group(function () {
  Route::get('/', [JualController::class, 'index'])->name('index');
  Route::post('/', [JualController::class, 'store'])->name('store');
  Route::get('/create', [JualController::class, 'create'])->name('create');
  Route::get('/{id}', [JualController::class, 'show'])->name('show');
  Route::get('/{id}/add-item', [JualController::class, 'addItem'])->name('add-item');
  Route::get('/{id}/edit', [JualController::class, 'edit'])->name('edit');
  Route::patch('/{id}/edit', [JualController::class, 'update'])->name('update');
  Route::get('/{id}/pdf/faktur', [JualController::class, 'exportFakur'])->name('faktur');
  Route::get('/{id}/pdf/spkb', [JualController::class, 'exportSpkb'])->name('spkb');
});

Route::prefix('surat-jalan')->as('surat-jalan.')->group(function () {
  Route::get('/', [SuratJalanController::class, 'index'])->name('index');
  Route::get('/excel', [SuratJalanController::class, 'exportExcel'])->name('excel');
  Route::get('/{id}', [SuratJalanController::class, 'show'])->name('show');
});

Route::prefix('mutation')->as('mutation.')->group(function () {
  Route::get('/', [MutationController::class, 'index'])->name('index');
  Route::get('/excel', [MutationController::class, 'exportExcelMutation'])->name('excel-mutation');
  Route::get('/kartu-stock', [MutationController::class, 'kartuStock'])->name('kartu-stock');
});

Route::prefix('persediaan')->as('persediaan.')->group(function () {
  Route::get('/', [PersediaanController::class, 'index'])->name('index');
  Route::get('/excel', [PersediaanController::class, 'exportExcel'])->name('excel');
});