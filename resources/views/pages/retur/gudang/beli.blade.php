@extends('layouts.main-layout')

@section('title', 'Verifikasi Retur Pembelian')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />

    <x-page-header title="Verifikasi Retur Pembelian" class="mb-3" />

    <div class="alert alert-info py-2 small mb-3">
      <i class="bi bi-exclamation-triangle"></i> <strong>Retur Pembelian:</strong> Verifikasi barang yang akan dikembalikan
      ke Supplier. Stok gudang akan <strong>berkurang</strong> jika disetujui.
    </div>

    <div class="card">
      <div class="card-header p-3">
        <p class="mb-0 fw-semibold">Tabel Data Retur Pembelian</p>
      </div>
      <div class="card-body">
        @livewire('table.retur-verifikasi-table', ['tipeTransaksi' => 'beli'])
      </div>
    </div>
  </div>

@endsection
