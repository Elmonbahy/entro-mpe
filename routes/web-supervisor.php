<?php

use App\Http\Controllers\Supervisor\MutationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Supervisor\BrandController;
use App\Http\Controllers\Supervisor\PelangganController;
use App\Http\Controllers\Supervisor\BarangController;
use App\Http\Controllers\Supervisor\SalesmanController;
use App\Http\Controllers\Supervisor\SupplierController;
use App\Http\Controllers\Supervisor\BeliController;
use App\Http\Controllers\Supervisor\BarangStockController;
use App\Http\Controllers\Supervisor\JualController;
use App\Http\Controllers\Supervisor\UsersController;

Route::resource('/barang', BarangController::class);
Route::resource('/brand', BrandController::class);
Route::resource('/pelanggan', PelangganController::class);
Route::resource('/salesman', SalesmanController::class);
Route::resource('/supplier', SupplierController::class);

Route::prefix('user')->as('user.')->group(function () {
  Route::get('/', [UsersController::class, 'index'])->name('index');
});

Route::prefix('export')->group(function () {
  Route::get('/barang', [BarangController::class, 'exportExcel'])->name('barang.export');
  Route::get('/brand', [BrandController::class, 'exportExcel'])->name('brand.export');
  Route::get('/pelanggan', [PelangganController::class, 'exportExcel'])->name('pelanggan.export');
  Route::get('/supplier', [SupplierController::class, 'exportExcel'])->name('supplier.export');
});

Route::prefix('beli')->as('beli.')->group(function () {
  Route::get('/', [BeliController::class, 'index'])->name('index');
  Route::get('/{id}', [BeliController::class, 'show'])->name('show');
});

Route::prefix('stock')->as('stock.')->group(function () {
  Route::get('/', [BarangStockController::class, 'index'])->name('index');
  Route::get('/excel', [BarangStockController::class, 'exportExcel'])->name('excel');
});

Route::prefix('jual')->as('jual.')->group(function () {
  Route::get('/', [JualController::class, 'index'])->name('index');
  Route::get('/{id}', [JualController::class, 'show'])->name('show');
});

Route::prefix('mutation')->as('mutation.')->group(function () {
  Route::get('/', [MutationController::class, 'index'])->name('index');
  Route::get('/excel', [MutationController::class, 'exportExcelMutation'])->name('excel-mutation');
  Route::get('/kartu-stock', [MutationController::class, 'kartuStock'])->name('kartu-stock');
});