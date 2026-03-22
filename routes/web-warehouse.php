<?php

use App\Http\Controllers\Warehouse\JualController;
use App\Http\Controllers\Warehouse\JualDetailController;
use App\Http\Controllers\Warehouse\BeliDetailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Warehouse\BarangController;
use App\Http\Controllers\Warehouse\BeliController;
use App\Http\Controllers\Warehouse\SuratJalanController;


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
