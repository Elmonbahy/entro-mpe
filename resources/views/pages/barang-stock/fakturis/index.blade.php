@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data stock barang" class="mb-3" />

    <div class="card">
      <div class="p-3 card-header d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Tabel data stock barang</p>
      </div>

      <div class="p-3">
        <div class="dropdown d-inline-block">
          <button class="btn btn-secondary dropdown-toggle" type="button" data-coreui-toggle="dropdown"
            aria-expanded="false">
            <i class="bi bi-file-earmark-arrow-down me-1"></i>
          </button>
          <ul class="dropdown-menu">
            <li>
              <a class="dropdown-item" href="{{ route('fakturis.stock.excel') }}">
                <i class="bi bi-file-earmark-excel me-2"></i> Stock Barang
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('fakturis.stock.exportbarangperbatchExcel') }}">
                <i class="bi bi-file-earmark-excel me-2"></i> Stock Barang Per Batch
              </a>
            </li>
          </ul>
        </div>
        @livewire('table.barang-stock-table')
      </div>
    </div>
  </div>
@endsection
