<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pajak\BeliController;
use App\Http\Controllers\Pajak\JualController;
use App\Http\Controllers\Pajak\SuratJalanController;

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
    Route::get('/{id}', [SuratJalanController::class, 'show'])->name('show');
});

