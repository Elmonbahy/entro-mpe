<?php

use App\Http\Controllers\Gudang\JualController;
use App\Http\Controllers\Gudang\JualDetailController;
use App\Http\Controllers\Gudang\BarangStockController;
use App\Http\Controllers\Gudang\BeliDetailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Gudang\BarangController;
use App\Http\Controllers\Gudang\BeliController;
use App\Http\Controllers\Gudang\BarangRusakController;
use App\Http\Controllers\Gudang\SuratJalanController;
use App\Http\Controllers\Gudang\PelangganController;
use App\Http\Controllers\Gudang\KendaraanController;
use App\Http\Controllers\Gudang\MutationController;
use App\Http\Controllers\Gudang\Laporan\Beli\LaporanBeliController;
use App\Http\Controllers\Gudang\Laporan\Jual\LaporanJualController;
use App\Http\Controllers\Gudang\Laporan\Pengiriman\LaporanPengirimanController;
use App\Http\Controllers\Gudang\Laporan\ListPengiriman\LaporanListPengirimanController;
use App\Http\Controllers\Gudang\Laporan\Pending\LaporanPendingController;
use App\Http\Controllers\Gudang\BarangExpiredController;
use App\Http\Controllers\Gudang\ReturController;

Route::resource('/barang', BarangController::class);
Route::resource('/pelanggan', PelangganController::class);
Route::resource('/kendaraan', KendaraanController::class);

Route::prefix('export')->group(function () {
  Route::get('/barang', [BarangController::class, 'exportExcel'])->name('barang.export');
  Route::get('/pelanggan', [PelangganController::class, 'exportExcel'])->name('pelanggan.export');
});

Route::prefix('beli')->as('beli.')->group(function () {
  Route::get('/', [BeliController::class, 'index'])->name('index');
  Route::get('/{id}', [BeliController::class, 'show'])->name('show');
  Route::patch('/{id}/done', [BeliController::class, 'done'])->name('done');
  Route::put('/retur/update-keterangan/{id}', [BeliController::class, 'updateKeterangan'])->name('updateKeterangan');

  Route::get('/retur/{id}', [BeliDetailController::class, 'returDone'])->name('retur-done');
  Route::get('/{id}/retur/{beli_detail_id}', [BeliDetailController::class, 'retur'])->name('retur-item');
  Route::patch('/{id}/retur/{beli_detail_id}', [BeliDetailController::class, 'returUpdate'])->name('retur-update');

  Route::get('/{id}/stock/{beli_detail_id}', [BeliDetailController::class, 'stock'])->name('stock-item');
  Route::patch('/{id}/stock/{beli_detail_id}', [BeliDetailController::class, 'stockUpdate'])->name('stock-item-update');

  Route::patch('{id}/stock/{beli_detail_id}/split', [BeliDetailController::class, 'splitRemainingStock'])->name('split-stock');
});

Route::prefix('stock')->as('stock.')->group(function () {
  Route::get('/', [BarangStockController::class, 'index'])->name('index');
});

Route::prefix('mutation')->as('mutation.')->group(function () {
  Route::get('/', [MutationController::class, 'index'])->name('index');
  Route::get('/excel', [MutationController::class, 'exportExcelMutation'])->name('excel-mutation');
  Route::get('/kartu-stock', [MutationController::class, 'kartuStock'])->name('kartu-stock');
  Route::get('/excel/kartu-stock', [MutationController::class, 'exportExcelKartuStock'])->name('excel-kartu-stock');
});

Route::prefix('barang-rusak')->as('barang-rusak.')->group(function () {
  Route::get('/', [BarangRusakController::class, 'index'])->name('index');
  Route::get('/create', [BarangRusakController::class, 'create'])->name('create');
  Route::get('/excel', [BarangRusakController::class, 'exportExcel'])->name('excel');
  Route::get('/{id}', [BarangRusakController::class, 'show'])->name('show');
});

Route::prefix('barang-expired')->as('barang-expired.')->group(function () {
  Route::get('/', [BarangExpiredController::class, 'index'])->name('index');
  Route::get('/excel', [BarangExpiredController::class, 'exportExcel'])->name('excel');
});

Route::prefix('jual')->as('jual.')->group(function () {
  Route::get('/', [JualController::class, 'index'])->name('index');
  Route::get('/{id}', [JualController::class, 'show'])->name('show');
  Route::patch('/{id}/done', [JualController::class, 'done'])->name('done');
  Route::get('/{id}/pdf', [JualController::class, 'exportPdf'])->name('pdf');
  Route::put('/retur/update-keterangan/{id}', [JualController::class, 'updateKeterangan'])->name('updateKeterangan');

  Route::get('/retur/{id}', [JualDetailController::class, 'returDone'])->name('retur-done');
  Route::get('/{id}/retur/{jual_detail_id}', [JualDetailController::class, 'retur'])->name('retur-item');
  Route::patch('/{id}/retur/{jual_detail_id}', [JualDetailController::class, 'returUpdate'])->name('retur-update');

  // stock barang masuk
  Route::get('/{id}/stock/{jual_detail_id}', [JualDetailController::class, 'stock'])->name('stock-item');
  Route::patch('/{id}/stock/{jual_detail_id}', [JualDetailController::class, 'stockUpdate'])->name('stock-item-update');

  Route::patch('{id}/stock/{jual_detail_id}/split', [JualDetailController::class, 'splitRemainingStock'])->name('split-stock');
});

Route::prefix('surat-jalan')->as('surat-jalan.')->group(function () {
  Route::get('/', [SuratJalanController::class, 'index'])->name('index');
  Route::post('/', [SuratJalanController::class, 'store'])->name('store');
  Route::get('/create', [SuratJalanController::class, 'create'])->name('create');
  Route::get('/{id}/pdf', [SuratJalanController::class, 'exportPdf'])->name('pdf');
  Route::get('/excel', [SuratJalanController::class, 'exportExcel'])->name('excel');
  Route::get('/{id}', [SuratJalanController::class, 'show'])->name('show');
  Route::get('/{id}/add-item', [SuratJalanController::class, 'addItem'])->name('add-item');
  Route::get('/{id}/edit', [SuratJalanController::class, 'edit'])->name('edit');
  Route::patch('/{id}/edit', [SuratJalanController::class, 'update'])->name('update');
});

Route::prefix('laporan-beli')->as('laporan-beli.')->group(function () {
  Route::get('/', [LaporanBeliController::class, 'index'])->name('index');
  Route::get('/excel', [LaporanBeliController::class, 'exportExcel'])->name('excel');
});

Route::prefix('laporan-jual')->as('laporan-jual.')->group(function () {
  Route::get('/', [LaporanJualController::class, 'index'])->name('index');
  Route::get('/excel', [LaporanJualController::class, 'exportExcel'])->name('excel');
});

Route::prefix('laporan-pengiriman')->as('laporan-pengiriman.')->group(function () {
  Route::get('/', [LaporanPengirimanController::class, 'index'])->name('index');
  Route::get('/excel', [LaporanPengirimanController::class, 'exportExcel'])->name('excel');
});

Route::prefix('laporan-list-pengiriman')->as('laporan-list-pengiriman.')->group(function () {
  Route::get('/', [LaporanListPengirimanController::class, 'index'])->name('index');
  Route::get('/excel', [LaporanListPengirimanController::class, 'exportExcel'])->name('excel');
});

Route::prefix('laporan-pending')->as('laporan-pending.')->group(function () {
  Route::get('/', [LaporanPendingController::class, 'index'])->name('index');
  Route::get('/excel', [LaporanPendingController::class, 'exportExcel'])->name('excel');
});

Route::prefix('retur')->as('retur.')->group(function () {
  // Route Verifikasi Retur Penjualan (Masuk)
  Route::get('/jual', [ReturController::class, 'indexJual'])
    ->name('jual');

  // Route Verifikasi Retur Pembelian (Keluar)
  Route::get('/beli', [ReturController::class, 'indexBeli'])
    ->name('beli');

  // Halaman Detail Retur
  Route::get('detail/{id}', [ReturController::class, 'show'])
    ->name('show');

  //  Action Verifikasi (Approve/Reject) dari halaman detail
  Route::patch('verify/{id}', [ReturController::class, 'verify'])
    ->name('verify');
});