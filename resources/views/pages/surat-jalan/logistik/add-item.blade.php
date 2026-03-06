@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Surat Jalan" class="mb-3" />

    <div class="mb-3">
      <x-card.surat-jalan-detail :data="$surat_jalan" />
    </div>

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Form Tambah Barang</p>
      </div>

      <div class="card-body">
        <div class="col-md-4 mb-3">
          <livewire:surat-jalan.select-faktur :pelanggan_id="$surat_jalan->pelanggan_id" />
        </div>
        <livewire:surat-jalan.daftar-barang :surat_jalan_id="$surat_jalan->id" />
      </div>
    </div>

    <livewire:surat-jalan.daftar-barang-dikirim :surat_jalan_id="$surat_jalan->id" />
  </div>
@endsection
