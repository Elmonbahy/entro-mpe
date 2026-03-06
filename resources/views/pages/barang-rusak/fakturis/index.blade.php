@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel data barang rusak</p>
      </div>

      <div class="p-3">
        <a href="{{ route('fakturis.barang-rusak.excel') }}" class="btn btn-secondary" title="Download Excel">
          <i class="bi bi-file-earmark-arrow-down"></i>
        </a>
        @livewire('table.barang-rusak-table')
      </div>
    </div>
  </div>
@endsection
