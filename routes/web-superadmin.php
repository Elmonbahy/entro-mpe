<?php

use App\Http\Controllers\Superadmin\BarangController;
use App\Http\Controllers\Superadmin\BrandController;
use App\Http\Controllers\Superadmin\GroupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\RoleAccessController;

Route::prefix('manage-access')->as('manage-access.')->group(function () {
  Route::get('/', [RoleAccessController::class, 'index'])->name('index');
  Route::patch('/manage-access/{id}/toggle', [RoleAccessController::class, 'toggle'])->name('toggle');
});

Route::resource('/barang', BarangController::class);
Route::resource('/brand', BrandController::class);
Route::resource('/group', GroupController::class);

Route::prefix('export')->group(function () {
  Route::get('/barang', [BarangController::class, 'exportExcel'])->name('barang.export');
  Route::get('/brand', [BrandController::class, 'exportExcel'])->name('brand.export');
  Route::get('/group', [GroupController::class, 'exportExcel'])->name('group.export');
  Route::get('/pelanggan', [PelangganController::class, 'exportExcel'])->name('pelanggan.export');
  Route::get('/supplier', [SupplierController::class, 'exportExcel'])->name('supplier.export');
});