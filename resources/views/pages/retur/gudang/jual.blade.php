@extends('layouts.main-layout')

@section('title', 'Verifikasi Retur Penjualan')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />

    <x-page-header title="Verifikasi Retur Penjualan" class="mb-3" />

    <div class="alert alert-info py-2 small mb-3">
      <i class="bi bi-info-circle"></i> <strong>Retur Penjualan:</strong> Verifikasi barang yang dikembalikan oleh
      Customer. Stok gudang akan <strong>bertambah</strong> jika disetujui.
    </div>

    <div class="card">
      <div class="card-header p-3 ">
        <p class="mb-0 fw-semibold">Tabel Data Retur Penjualan</p>
      </div>
      <div class="card-body">
        @livewire('table.retur-verifikasi-table', ['tipeTransaksi' => 'jual'])
      </div>
    </div>
  </div>

@endsection
