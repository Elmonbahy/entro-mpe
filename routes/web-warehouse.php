<?php

use App\Http\Controllers\Warehouse\JualController;
use App\Http\Controllers\Warehouse\JualDetailController;
use App\Http\Controllers\Gudang\BarangStockController;
use App\Http\Controllers\Warehouse\BeliDetailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Warehouse\BarangController;
use App\Http\Controllers\Warehouse\BeliController;
use App\Http\Controllers\Gudang\BarangRusakController;
use App\Http\Controllers\Warehouse\SuratJalanController;
use App\Http\Controllers\Gudang\PelangganController;
use App\Http\Controllers\Gudang\KendaraanController;
use App\Http\Controllers\Gudang\MutationController;
use App\Http\Controllers\Gudang\Laporan\Beli\LaporanBeliController;
use App\Http\Controllers\Gudang\Laporan\Jual\LaporanJualController;
use App\Http\Controllers\Gudang\Laporan\Pengiriman\LaporanPengirimanController;
use App\Http\Controllers\Gudang\Laporan\Pending\LaporanPendingController;
use App\Http\Controllers\Gudang\BarangExpiredController;


Route::resource('/barang', BarangController::class);

Route::prefix('export')->group(function () {
    Route::get('/barang', [BarangController::class, 'exportExcel'])->name('barang.export');
});

Route::prefix('beli')->as('beli.')->group(function () {
    Route::get('/', [BeliController::class, 'index'])->name('index');
    Route::get('/{id}', [BeliController::class, 'show'])->name('show');
    Route::put('/update-nie/{id}', [BeliController::class, 'updateNie'])->name('updateNie');
    Route::get('/{id}/pdf/do', [BeliController::class, 'exportDo'])->name('do');
    Route::get('/{id}/pdf/retur', [BeliController::class, 'exportRetur'])->name('retur');

    Route::get('/{beli_id}/pdf/qc-all', [BeliDetailController::class, 'exportQcAll'])->name('qc-all');
    Route::get('/{beli_id}/{beli_detail_id}/pdf/qc', [BeliDetailController::class, 'exportQc'])->name('qc');
});

// Route::prefix('stock')->as('stock.')->group(function () {
//     Route::get('/', [BarangStockController::class, 'index'])->name('index');
//     Route::get('/excel', [BarangStockController::class, 'exportExcel'])->name('excel');
// });

// Route::prefix('mutation')->as('mutation.')->group(function () {
//     Route::get('/', [MutationController::class, 'index'])->name('index');
//     Route::get('/excel', [MutationController::class, 'exportExcelMutation'])->name('excel-mutation');
//     Route::get('/kartu-stock', [MutationController::class, 'kartuStock'])->name('kartu-stock');
// });

// Route::prefix('barang-rusak')->as('barang-rusak.')->group(function () {
//     Route::get('/', [BarangRusakController::class, 'index'])->name('index');
//     Route::get('/create', [BarangRusakController::class, 'create'])->name('create');
//     Route::get('/{id}', [BarangRusakController::class, 'show'])->name('show');
// });

// Route::prefix('barang-expired')->as('barang-expired.')->group(function () {
//     Route::get('/', [BarangExpiredController::class, 'index'])->name('index');
//     Route::get('/excel', [BarangExpiredController::class, 'exportExcel'])->name('excel');
// });

Route::prefix('jual')->as('jual.')->group(function () {
    Route::get('/', [JualController::class, 'index'])->name('index');
    Route::get('/{id}', [JualController::class, 'show'])->name('show');
    Route::put('/update-nie/{id}', [JualController::class, 'updateNie'])->name('updateNie');
    Route::get('/{id}/pdf/do', [JualController::class, 'exportDo'])->name('do');
    Route::get('/{id}/pdf/retur', [JualController::class, 'exportRetur'])->name('retur');

    Route::get('/{jual_id}/pdf/qc-all', [JualDetailController::class, 'exportQcAll'])->name('qc-all');
    Route::get('/{jual_id}/{jual_detail_id}/pdf/qc', [JualDetailController::class, 'exportQc'])->name('qc');
});

Route::prefix('surat-jalan')->as('surat-jalan.')->group(function () {
    Route::get('/', [SuratJalanController::class, 'index'])->name('index');
    Route::get('/excel', [SuratJalanController::class, 'exportExcel'])->name('excel');
    Route::get('/{id}', [SuratJalanController::class, 'show'])->name('show');
});

// Route::prefix('laporan-beli')->as('laporan-beli.')->group(function () {
//     Route::get('/', [LaporanBeliController::class, 'index'])->name('index');
//     Route::get('/excel', [LaporanBeliController::class, 'exportExcel'])->name('excel');
// });

// Route::prefix('laporan-jual')->as('laporan-jual.')->group(function () {
//     Route::get('/', [LaporanJualController::class, 'index'])->name('index');
//     Route::get('/excel', [LaporanJualController::class, 'exportExcel'])->name('excel');
// });

// Route::prefix('laporan-pengiriman')->as('laporan-pengiriman.')->group(function () {
//     Route::get('/', [LaporanPengirimanController::class, 'index'])->name('index');
//     Route::get('/excel', [LaporanPengirimanController::class, 'exportExcel'])->name('excel');
// });

// Route::prefix('laporan-pending')->as('laporan-pending.')->group(function () {
//     Route::get('/', [LaporanPendingController::class, 'index'])->name('index');
//     Route::get('/excel', [LaporanPendingController::class, 'exportExcel'])->name('excel');
// });
